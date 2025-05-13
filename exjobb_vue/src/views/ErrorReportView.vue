<!--Vy för hantering av felrapporter-->
<template>
  <div>
    <h1 class="text-3xl text-center mt-4 mb-4">Felanmälningar</h1>
    <!--Div som visas om det finns felanmälningar. OBS!! bör visa alternativ text om det inte finns några!-->
    <div v-if="errorReports" class="mb-12">
      <label for="search" class="mx-auto block w-fit mt-4 mt-1 font-semibold text-lg"
        >Sök felanmälan</label
      >
      <!--En filtreringsfunktion för felanmälningar. Filtrerar endast för titel -->
      <input
        aria-label="Sök"
        type="search"
        id="search"
        class="bg-white mx-auto block shadow-sm rounded-3xl pl-2 pr-2 pt-1 pb-1 mb-4"
        name="search"
        placeholder="Sök"
        @input="filterErrorReports"
        v-model="filterTerm"
      />
      <!--Tabell för att dynamisk skriva ut felanmälningar. 
      Använder data-label för visning på små skärmar. Sorterar data genom klick i headern-->
      <table class="">
        <thead>
          <tr>
            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortErrorReportNumber"
            >
              Ärendenummer
            </th>
            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortAccommodation"
            >
              Bostad
            </th>
            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortTitle"
            >
              Titel
            </th>
            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortStatus"
            >
              Status
            </th>

            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortCreated"
            >
              Skapad
            </th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">
              Se hela
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(errorReport, index) in paginationErrorReports"
            :key="index"
            :class="{ 'line-through': errorReport.status === 'discarded' }"
          >
            <td data-label="Ärendenummer">{{ errorReport.id }}</td>
            <td data-label="Bostad">{{ errorReport.accommodation_ref }}</td>
            <td data-label="Titel">{{ errorReport.title }}</td>
            <td
              data-label="Status"
              :class="{
                'bg-green-100': errorReport.status === 'completed',
                'bg-orange-100': errorReport.status === 'started',
                'bg-red-100': errorReport.status === 'not_started',
              }"
            >
              {{
                errorReport.status === 'completed'
                  ? 'Utfärdad'
                  : errorReport.status === 'started'
                    ? 'Pågående'
                    : errorReport.status === 'not_started'
                      ? 'Ej påbörjad'
                      : errorReport.status === 'discarded'
                        ? 'Makulerad'
                        : 'Okänd'
              }}
            </td>
            <td data-label="Skapad">
              {{ errorReport.created }}
            </td>
            <!--Knapp som öppnar en modal med hela felanmälan samt val att ändra status och ta bort-->
            <td data-label="Se hela" class="">
              <BlueButton class="" @clicked="openModal(errorReport)" buttonName="Visa" />
            </td>
          </tr>
        </tbody>
        <!--Table footer för paginering-->
        <tfoot class="bg-slate-200 w-full">
          <td colspan="6">
            Välj sida
            <span v-for="(pageNumb, index) in pageNumbers" :key="index">
              <button
                @click="choosePagination(pageNumb)"
                aria-label="Visa en sida med felanmälningar"
                :class="{ 'font-bold': currentPage === pageNumb }"
                class="text-blue-800 hover:font-bold cursor-pointer ml-1"
              >
                {{ pageNumb }}
              </button>
            </span>

            <button
              @click="allPagination()"
              aria-label="Visa alla felanmälningar"
              :class="{ 'font-bold': viewAll }"
              class="text-blue-800 hover:font-bold cursor-pointer ml-4"
            >
              Visa alla
            </button>
          </td>
        </tfoot>
      </table>

      <!--Modal till knapp ovan-->
      <ErrorReportModal
        @closeModal="closeModal"
        @getErrorReports="getErrorReports"
        :errorReport="errorReport"
      />

      <!--En fadad bakgrund till modal som också fungerar som stänga knapp-->
      <div
        class="bg-black w-full h-full opacity-35 fixed top-0 hidden"
        @click="closeModal"
        id="modalBG"
      ></div>
    </div>
  </div>
</template>

<script lang="ts">
import BlueButton from '@/components/BlueButton.vue'
import LargeContainer from '@/components/LargeContainer.vue'
import SlateButton from '@/components/SlateButton.vue'
import TextContentContainer from '@/components/TextContentContainer.vue'
import FormRealEstate from '@/components/FormRealEstate.vue'
import type { RealEstate } from '@/types/RealEstate.types'
import type { Accommodation } from '@/types/Accommodation.types'
import { getAPIAccommodations } from '@/apiService/AccommodationService'
import { getAPIRealEstates } from '@/apiService/RealEstateService'
import FormAccommodation from '@/components/FormAccommodation.vue'
import InvoiceModal from '@/components/InvoiceModal.vue'
import { getAPIErrorReports } from '@/apiService/ErrorReportService'
import type { ErrorReport } from '@/types/ErrorReport.types'
import ErrorReportModal from '@/components/ErrorReportModal.vue'

export default {
  name: 'InvoiceView',
  components: {
    BlueButton,
    SlateButton,
    LargeContainer,
    TextContentContainer,
    FormRealEstate,
    FormAccommodation,
    InvoiceModal,
    ErrorReportModal,
  },

  //Dataarray samt variabel och bool för felanmälningar och sortering + filtrering.
  data() {
    return {
      errorReports: [] as ErrorReport[],
      filteredErrorReports: [] as ErrorReport[],
      errorReport: {} as ErrorReport,
      filterTerm: '',
      sortErrorReport: '',
      sortedBy: '',
      sortReverseNext: false,
      realEstates: [] as RealEstate[],
      accommodations: [] as Accommodation[],
      //påbörjat paginering NEDAN datavariabler tillhör paginering
      paginationErrorReports: [] as ErrorReport[],
      pageNumbers: [] as number[],
      currentPage: 1,
      startIndex: 0,
      endIndex: 10,
      viewAll: false,
    }
  },

  //Vid app start kontrolleras inloggning OBS!! ska byggas om!
  mounted() {
    //Hämtar fastigheter, bostäder samt felanmälningar
    this.getRealEstates()
    this.getAccommodations()
    this.getErrorReports()
  },

  methods: {
    //Metod för att hämta fastigheter
    async getRealEstates() {
      this.realEstates = await getAPIRealEstates()
    },

    //Metod för att hämta bostäder
    async getAccommodations() {
      this.accommodations = await getAPIAccommodations()
    },

    //Metod för att hämta felanmälningar
    async getErrorReports() {
      this.errorReports = await getAPIErrorReports()
      this.filteredErrorReports = this.errorReports
      this.filterErrorReports()
    },

    //Enkel filtrering av felanmälningar, filtrerar i hela objekten så att sökningar innefattar även hela meddelandet
    filterErrorReports() {
      this.filteredErrorReports = this.errorReports.filter((errorReport) => {
        const searchTerm = this.filterTerm.toLowerCase()
        return (
          errorReport.title.toLowerCase().includes(searchTerm) ||
          errorReport.message.toLowerCase().includes(searchTerm) ||
          errorReport.messagetype.toLowerCase().includes(searchTerm) ||
          errorReport.email.toLowerCase().includes(searchTerm) ||
          (errorReport.name && errorReport.name.toLowerCase().includes(searchTerm)) ||
          errorReport.accommodation_ref.toLowerCase().includes(searchTerm) ||
          (errorReport.status && errorReport.status.toLowerCase().includes(searchTerm))
        )
      })
      this.pageination()
    },

    //Metod för att välja vilken sida av paginering som ska visas.
    choosePagination(numb: number) {
      this.viewAll = false
      this.currentPage = numb
      if (numb === 1) {
        this.startIndex = 0
        this.endIndex = 10
      } /*(this.startIndex === 1)*/ else {
        this.startIndex = 10 * (numb - 1)
        this.endIndex = 10 * numb
      }

      this.pageination()
    },

    //Metod för att alla istället för faginering.
    allPagination() {
      this.viewAll = true
      this.currentPage = 0
      this.pageination()
    },

    //Världens bästa egenskapade paginering
    pageination() {
      if (!this.viewAll) {
        this.pageNumbers = []
        let invoiceLength = this.filteredErrorReports.length
        let numberOfPages = Math.ceil(invoiceLength / 10)
        for (let i = 1; i <= numberOfPages; i++) {
          this.pageNumbers.push(i)
        }
        this.paginationErrorReports = this.filteredErrorReports.slice(
          this.startIndex,
          this.endIndex,
        )
      } else {
        this.paginationErrorReports = this.filteredErrorReports
      }
    },

    //sortering efter felrapportsnummer,
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortErrorReportNumber() {
      if (this.sortReverseNext && this.sortedBy === 'errorReport_number') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'errorReport_number'
        this.sortReverseNext = true
        this.filteredErrorReports = this.filteredErrorReports.sort((a, b) =>
          a.id!.localeCompare(b.id!),
        )
      }
      this.pageination()
    },

    //sortering efter bostad,
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortAccommodation() {
      if (this.sortReverseNext && this.sortedBy === 'accommodation') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'accommodation'
        this.sortReverseNext = true
        this.filteredErrorReports = this.filteredErrorReports.sort((a, b) =>
          a.accommodation_ref.localeCompare(b.accommodation_ref),
        )
      }
      this.pageination()
    },

    //sortering efter felrapportstitel,
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortTitle() {
      if (this.sortReverseNext && this.sortedBy === 'title') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'title'
        this.sortReverseNext = true
        this.filteredErrorReports = this.filteredErrorReports.sort((a, b) =>
          a.title.localeCompare(b.title),
        )
      }
      this.pageination()
    },

    //sortering efter datum skapad,
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortCreated() {
      if (this.sortReverseNext && this.sortedBy === 'created') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'created'
        this.sortReverseNext = true
        this.filteredErrorReports = this.filteredErrorReports.sort((a, b) => {
          // Konvertera strängarna till datumobjekt
          const dateA = new Date(a.created!)
          const dateB = new Date(b.created!)

          return dateA.getTime() - dateB.getTime()
        })
      }
      this.pageination()
    },

    //sortering efter status,
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortStatus() {
      if (this.sortReverseNext && this.sortedBy === 'status') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'status'
        this.sortReverseNext = true
        this.filteredErrorReports = this.filteredErrorReports.sort((a, b) =>
          a.status!.localeCompare(b.status!),
        )
      }
      this.pageination()
    },

    //omvänder sortering
    reverseSort() {
      this.filteredErrorReports = this.filteredErrorReports.reverse()
      this.pageination()
    },

    //öppnar modal för enskild felrapport
    openModal(errorReport: ErrorReport) {
      this.errorReport = errorReport
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'block'
      ;(document.getElementById('modalErrorReport') as HTMLElement).style.display = 'block'
    },

    //stränger modal för enskild felrapport
    closeModal() {
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'none'
      ;(document.getElementById('modalErrorReport') as HTMLElement).style.display = 'none'
      this.errorReport = {} as ErrorReport
    },
  },
}
</script>

<style lang="css" scoped>
table {
  border: 1px solid #ccc;
  border-collapse: collapse;
  margin: 0;
  padding: 0;
  width: 100%;
  table-layout: fixed;
}

table caption {
  font-size: 1.5em;
  margin: 0.5em 0 0.75em;
}

table tr {
  background-color: #f8f8f8;
  border: 1px solid #ddd;
  padding: 0.35em;
}

table th,
table td {
  padding: 0.625em;
  text-align: center;
}

table th {
  font-size: 0.85em;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

@media screen and (max-width: 1024px) {
  table {
    border: 0;
  }

  table caption {
    font-size: 1.3em;
  }

  table thead {
    border: none;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    width: 1px;
  }

  table tr {
    border-bottom: 3px solid #ddd;
    display: block;
    margin-bottom: 0.625em;
  }

  table td {
    border-bottom: 1px solid #ddd;
    display: block;
    font-size: 0.8em;
    text-align: right;
  }

  table td::before {
    /*
      * aria-label has no advantage, it won't be read inside a table
      content: attr(aria-label);
      */
    content: attr(data-label);
    float: left;
    font-weight: bold;
    text-transform: uppercase;
  }

  table td:last-child {
    border-bottom: 0;
  }
}

/* general styling */
body {
  font-family: 'Open Sans', sans-serif;
  line-height: 1.25;
}
</style>
