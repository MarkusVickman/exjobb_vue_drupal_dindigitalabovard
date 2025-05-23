<!--Vy för hantering av fakturor-->
<template>
  <div>
    <h1 class="text-3xl text-center mt-4 mb-4">Fakturor</h1>
    <p v-if="!invoices" class="text-l text-center">Laddar fakturor..</p>

    <!--Div som visas om det finns invoices. OBS!! bör visa alternativ text om det inte finns några!-->
    <div v-if="invoices" class="mb-12">
      <label for="search" class="mx-auto block w-fit mt-4 mt-1 font-semibold text-lg"
        >Sök faktura</label
      >
      <!--En filtreringsfunktion för fakturor. Filtrerar endast för bostad(titel) -->
      <input
        type="search"
        id="search"
        class="bg-white mx-auto block shadow-sm rounded-3xl pl-2 pr-2 pt-1 pb-1 mb-4"
        name="search"
        @input="filterInvoice"
        v-model="filterTerm"
        placeholder="Sök"
        aria-label="Sök"
      />
      <!--Tabell för att dynamisk skriva ut fakturor. 
      Använder data-label för visning på små skärmar. Sorterar data genom klick i headern-->
      <table class="">
        <thead>
          <tr>
            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortInvoiceNumber()"
            >
              Fakturanummer
            </th>
            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortTitle()"
            >
              Bostad
            </th>
            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortTenant()"
            >
              Bostadsgäst
            </th>
            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortStatus()"
            >
              Status
            </th>

            <th
              class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200"
              scope="col"
              @click="sortCreated()"
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
            v-for="(invoice, index) in paginationInvoices"
            :key="index"
            :class="{ 'line-through': invoice.status === 'discarded' }"
          >
            <td data-label="Fakturanummer">{{ invoice.invoice_number }}</td>
            <td data-label="Bostad">{{ invoice.title }}</td>
            <td data-label="Bostadsgäst">{{ invoice.tenant_name }}</td>
            <td
              data-label="Status"
              :class="{
                'bg-green-100': invoice.status === 'payed',
                'bg-red-100': invoice.status === 'not_payed' || invoice.status === 'reminded',
              }"
            >
              {{
                invoice.status === 'discarded'
                  ? 'Makulerad'
                  : invoice.status === 'payed'
                    ? 'Betald'
                    : invoice.status === 'not_payed'
                      ? 'Ej betald'
                      : invoice.status === 'reminded'
                        ? 'Påmind'
                        : 'Okänd'
              }}
            </td>
            <td data-label="Skapad">
              {{ invoice.created }}
            </td>
            <!--Knapp som öppnar en modal med hela fakturan samt val att ändra status och ta bort-->
            <td data-label="Se hela" class="">
              <BlueButton class="" @clicked="openModal(invoice)" buttonName="Visa" />
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
                aria-label="Visa en sida med fastigheter"
                :class="{ 'font-bold': currentPage === pageNumb }"
                class="text-blue-800 hover:font-bold cursor-pointer ml-1"
              >
                {{ pageNumb }}
              </button>
            </span>

            <button
              @click="allPagination()"
              aria-label="Visa alla fakturor"
              :class="{ 'font-bold': viewAll }"
              class="text-blue-800 hover:font-bold cursor-pointer ml-4"
            >
              Visa alla
            </button>
          </td>
        </tfoot>
      </table>

      <!--Modal till knapp ovan-->
      <InvoiceModal @closeModal="closeModal" @getInvoices="getInvoices" :invoice="invoice" />

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
import FormAccommodation from '@/components/FormAccommodation.vue'
import { getAPIInvoice } from '@/apiService/InvoiceService'
import type { Invoice } from '@/types/Invoice.types'
import InvoiceModal from '@/components/InvoiceModal.vue'

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
  },

  //Dataarray samt variabel och bool för fakturor och sortering + filtrering.
  data() {
    return {
      invoices: [] as Invoice[],
      filteredInvoices: [] as Invoice[],
      invoice: {} as Invoice,
      filterTerm: '',
      sortInvoice: '',
      sortedBy: '',
      sortReverseNext: false,
      //påbörjat paginering NEDAN datavariabler tillhör paginering
      paginationInvoices: [] as Invoice[],
      pageNumbers: [] as number[],
      currentPage: 1,
      startIndex: 0,
      endIndex: 10,
      viewAll: false,
    }
  },

  //Vid vyladdning
  mounted() {
    //Hämtar fakturor
    this.getInvoices()
  },

  methods: {
    //Metod för att hämta fakturor, initierar även den tidigare filtreringen för en bättre upplevelse för användaren
    async getInvoices() {
      this.invoices = await getAPIInvoice()
      this.filteredInvoices = this.invoices
      this.filterInvoice()
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
        let invoiceLength = this.filteredInvoices.length
        let numberOfPages = Math.ceil(invoiceLength / 10)
        for (let i = 1; i <= numberOfPages; i++) {
          this.pageNumbers.push(i)
        }
        this.paginationInvoices = this.filteredInvoices.slice(this.startIndex, this.endIndex)
      } else {
        this.paginationInvoices = this.filteredInvoices
      }
    },

    //Enkel filtrering av fakturor, filrerar hela objectet inte bara det som syns
    filterInvoice() {
      this.filteredInvoices = this.invoices.filter((invoice) => {
        const searchTerm = this.filterTerm.toLowerCase()
        return (
          invoice.title.toLowerCase().includes(searchTerm) ||
          invoice.tenant_name.toLowerCase().includes(searchTerm) ||
          invoice.invoice_number.toString().includes(searchTerm) ||
          invoice.status.toLowerCase().includes(searchTerm) ||
          invoice.email.toLowerCase().includes(searchTerm) ||
          invoice.invoice_html.toLowerCase().includes(searchTerm)
        )
      })
      this.pageination()
    },

    //sortering efter faktura nummer,
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortInvoiceNumber() {
      if (this.sortReverseNext && this.sortedBy === 'invoice_number') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'invoice_number'
        this.sortReverseNext = true
        this.filteredInvoices = this.filteredInvoices.sort(
          (b, a) => a.invoice_number - b.invoice_number,
        )
      }
      this.pageination()
    },

    //sortering efter status
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortStatus() {
      if (this.sortReverseNext && this.sortedBy === 'status') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'status'
        this.sortReverseNext = true
        this.filteredInvoices = this.filteredInvoices.sort((a, b) =>
          a.status.localeCompare(b.status),
        )
      }
      this.pageination()
    },

    //sortering efter den boendes namn
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortTenant() {
      if (this.sortReverseNext && this.sortedBy === 'tenant') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'tenant'
        this.sortReverseNext = true
        this.filteredInvoices = this.filteredInvoices.sort((a, b) =>
          a.tenant_name.localeCompare(b.tenant_name),
        )
      }
      this.pageination()
    },

    //sortering efter skapat datum
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortCreated() {
      if (this.sortReverseNext && this.sortedBy === 'created') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'created'
        this.sortReverseNext = true
        this.filteredInvoices = this.filteredInvoices.sort((a, b) => {
          // Konvertera strängarna till datumobjekt
          const dateA = new Date(a.created)
          const dateB = new Date(b.created)

          return dateA.getTime() - dateB.getTime()
        })
      }
      this.pageination()
    },

    //sortering efter bostadsnamn
    // Ändrar och kontrollerar om den sorterats med denna funktion förra gången isåfall omvänds filtreringen
    sortTitle() {
      if (this.sortReverseNext && this.sortedBy === 'title') {
        this.sortReverseNext = false
        this.reverseSort()
      } else {
        this.sortedBy = 'title'
        this.sortReverseNext = true
        this.filteredInvoices = this.filteredInvoices.sort((a, b) => a.title.localeCompare(b.title))
      }
      this.pageination()
    },

    //omvänd sortering
    reverseSort() {
      this.filteredInvoices = this.filteredInvoices.reverse()
      this.pageination()
    },

    //öppna modal för enskilld faktura
    openModal(invoice: Invoice) {
      this.invoice = invoice
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'block'
      ;(document.getElementById('modalInvoice') as HTMLElement).style.display = 'block'
    },

    //stäng modal för enskilld faktura
    closeModal() {
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'none'
      ;(document.getElementById('modalInvoice') as HTMLElement).style.display = 'none'
      this.invoice = {} as Invoice
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
