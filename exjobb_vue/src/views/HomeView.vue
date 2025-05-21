<!--Vy för hantering av fastigheter och tillhörande bostäder-->
<template>
  <div>
    <h1 class="text-3xl text-center mt-4 mb-4">Fastigheter</h1>
    <div class="lg:flex mx-auto max-w-screen-2xl">
      <!--Listar alla fastigheter i en container-->
      <LargeContainer title="Fastigheter">
        <div v-for="(realEstate, index) in realEstates" :key="index">
          <TextContentContainer
            :id="realEstate.id"
            :title="realEstate.title"
            :text1="realEstate.streetaddress"
            :text2="realEstate.invoice_due_date"
            :text3="realEstate.invoice_send_date"
            :text4="(realEstate.autoinvoice === '1' as any) ? 'Aktiverad' : 'Avstängd'"
            :text5="realEstate.payment_method"
            :text6="realEstate.payment_number"
            text1Header="Gatuadress: "
            text2Header="Faktura förfallodag: "
            text3Header="Faktureringsdag: "
            text4Header="Auto-faktrutering: "
            text5Header="Betalningsmetod: "
            text6Header="Betalningsnummer: "
            class="mb-6 min-h-36"
          >
            <div class="w-fit mx-auto">
              <!--knappen öppnar en modal för redigering-->
              <BlueButton buttonName="Redigera" @clicked="editRealEstate(realEstate)" class="m-3" />
              <!--Knappan aktiverar en funktion som visar alla tillhörande bostäder-->
              <SlateButton
                buttonName="Bostäder"
                @clicked="displayAccommodations(realEstate)"
                class="m-3"
              />
            </div>
          </TextContentContainer>
        </div>
        <!--Knapp som öppnar ett formulär för att lägga till en fastighet-->
        <BlueButton buttonName="Lägg till" @clicked="addRealEstate" class="mx-auto block" />
      </LargeContainer>
      <!--För utseende och en scroll-div-->
      <div class="lg:w-0.5 lg:bg-slate-600" v-if="toggleAccommodation"></div>
      <div id="accScrollDiv"></div>
      <!--Listar alla bostäder i en container-->
      <LargeContainer :title="'Bostäder | ' + rETitle" v-if="toggleAccommodation">
        <div v-for="(accommodation, index) in relatedAccommodations" :key="index">
          <TextContentContainer
            :id="accommodation.id"
            :title="accommodation.title"
            :text1="accommodation.tenant"
            :text2="accommodation.emailaddress"
            :text3="accommodation.rent + 'kr'"
            text1Header="Hyresgäst: "
            text2Header="Epost-adress: "
            text3Header="Månadshyra: "
            class="mb-6 min-h-36"
          >
            <div class="w-fit mx-auto">
              <!--knappen öppnar en modal för redigering-->
              <BlueButton
                buttonName="Redigera"
                @clicked="editAccommodation(accommodation)"
                class="m-3"
              />
              <!-- knapp ska skicka en faktura, ej klar-->
              <SlateButton
                buttonName="Skicka faktura"
                @clicked="sendInvoice(accommodation)"
                class="m-3"
              />
            </div>
          </TextContentContainer>
        </div>
        <p v-if="errorMessage">{{ errorMessage }}</p>
        <!--Knapp som öppnar ett formulär för att lägga till en bostad-->
        <BlueButton buttonName="Lägg till" @clicked="addAccommodation" class="mx-auto block" />
      </LargeContainer>
    </div>
  </div>

  <div
    class="bg-black w-full h-full opacity-35 fixed top-0 hidden"
    @click="closeForm"
    id="modalBG"
  ></div>
  <!--modal-komponent för att lägga till ny eller ändra i en fastighet-->
  <FormRealEstate
    @abort="closeForm"
    id="formReal"
    :editRealEstateData="editRealEstateData"
    @getAccommodations="getAccommodations"
    @getRealEstates="getRealEstates"
    :isNew="isNew"
  />
  <!--modal-komponent för att lägga till ny eller ändra i en bostad-->
  <FormAccommodation
    @abort="closeForm"
    id="formAccom"
    :editAccommodationData="editAccommodationData"
    @getAccommodations="getAccommodations"
    @getRealEstates="getRealEstates"
    :rEId="rEId"
    :isNew="isNew"
  />
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
import { postInvoice } from '@/apiService/InvoiceService'

export default {
  name: 'HomeView',
  components: {
    BlueButton,
    SlateButton,
    LargeContainer,
    TextContentContainer,
    FormRealEstate,
    FormAccommodation,
  },

  //Dataarrayer samt variabel för meddelande och bools för att ändra status.
  data() {
    return {
      errorMessage: '',
      realEstates: [] as RealEstate[],
      accommodations: [] as Accommodation[],
      relatedAccommodations: [] as Accommodation[],
      editRealEstateData: {} as RealEstate,
      editAccommodationData: {} as Accommodation,
      toggleAccommodation: false,
      rETitle: '',
      isNew: true,
      rEId: '0',
      tempRealEstate: {} as RealEstate,
    }
  },

  //Vid vy-laddning hämtas data
  mounted() {
    //Hämtar data till vyn
    this.getRealEstates()
    this.getAccommodations()
  },

  methods: {
    //Hämtar fastigheter
    async getRealEstates() {
      this.realEstates = await getAPIRealEstates()
    },

    //Hämtar bostäder
    async getAccommodations() {
      this.accommodations = await getAPIAccommodations()
      if (Object.keys(this.tempRealEstate).length !== 0) {
        this.displayAccommodations(this.tempRealEstate)
      }
    },

    //Ändrar visning så och filtrerar så att endast bostäder relaterade till vald fastighet visas
    displayAccommodations(realEstate: RealEstate) {
      this.tempRealEstate = realEstate
      this.relatedAccommodations = this.accommodations.filter((accommodation) => {
        return accommodation.real_estate_id.some(
          (realEstateId) => realEstateId.target_id === realEstate.id,
        )
      })

      //Sätter rETitle som används för att skriva ut fastighetstiteln ovanför bostädernas
      this.rETitle = realEstate.title

      //används för att ändra visningsläge och visar högra spalten med de relaterade bostäderna
      this.toggleAccommodation = true

      //skickas med när en ny bostad skapas så att den kan relatera till fastigheten
      this.rEId = realEstate.id

      //Skrollar användaren mot det som ska visas på sidan
      var scrollDiv = (document.getElementById('accScrollDiv') as HTMLElement).offsetTop
      window.scrollTo({ top: scrollDiv, behavior: 'smooth' })

      //Nollställer meddelande
      this.errorMessage = ''
    },

    //metod för att lägga till en ny bostad
    addAccommodation() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
      this.isNew = true
      this.editAccommodationData = {} as Accommodation
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'block'
      ;(document.getElementById('formAccom') as HTMLElement).style.display = 'block'
    },

    //metod för att ändra i en bostad
    editAccommodation(accommodation: Accommodation) {
      window.scrollTo({ top: 0, behavior: 'smooth' })
      this.isNew = false
      this.editAccommodationData = accommodation
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'block'
      ;(document.getElementById('formAccom') as HTMLElement).style.display = 'block'
    },

    //metod för att lägga till en ny fastighet
    addRealEstate() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
      this.isNew = true
      this.editRealEstateData = {} as RealEstate
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'block'
      ;(document.getElementById('formReal') as HTMLElement).style.display = 'block'
    },

    //metod för att ändra i en fastighet
    editRealEstate(realEstate: RealEstate) {
      window.scrollTo({ top: 0, behavior: 'smooth' })
      this.isNew = false
      this.editRealEstateData = realEstate
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'block'
      ;(document.getElementById('formReal') as HTMLElement).style.display = 'block'
    },

    //Metod för att skicka faktura, ej klar!!!!!!
    async sendInvoice(accommodation: Accommodation) {
      let invoice = await postInvoice(accommodation)
      this.fetchResponse(invoice)
    },

    //Beroende på svara från submit och delete så uppdataras fomulär samt data eller så skrivs ett fel ut
    fetchResponse(check: any) {
      if (check) {
        this.errorMessage = 'Fakturan skickades'
      } else {
        this.errorMessage = 'Fakturan kunde inte skickas'
      }
    },

    //metod som stänger ner formuläret och den gråa bakgrunden. nollställer även data som finns i formuläret
    closeForm() {
      ;(document.getElementById('modalBG') as HTMLElement).style.display = 'none'
      ;(document.getElementById('formReal') as HTMLElement).style.display = 'none'
      ;(document.getElementById('formAccom') as HTMLElement).style.display = 'none'
      this.editAccommodationData = {} as Accommodation
      this.editRealEstateData = {} as RealEstate
    },
  },
}
</script>
