<!--Komponent för att ändra eller skapa nya fastigheter-->
<template>
  <div
    class="absolute overflow-scroll lg:overflow-auto align-center m-auto z-4 hidden lg:absolute lg:w-2xl lg:h-fit h-full w-full inset-0 bg-zinc-100 shadow-sm rounded-3xl flex items-center justify-center z-50 p-10"
  >
    <h2 class="text-2xl underline text-center">Fastighetsformulär</h2>
    <form @submit.prevent="submit" class="w-fit mx-auto p-6">
      <input
        type="hidden"
        id="RealEstateId"
        name="RealEstateId"
        v-model="formData.id"
        class="block drop-shadow-sm rounded-md bg-white"
      />
      <label for="REnamn" class="block mt-4 mt-1">Fastighetsnamn</label>
      <input
        placeholder="Fastighetsnamn"
        aria-label="Fastighetsnamn"
        type="text"
        id="REnamn"
        name="REnamn"
        required
        v-model="formData.title"
        class="block drop-shadow-sm rounded-md bg-white mt-1 pl-1 pr-1"
      />
      <label for="streetaddress" class="block mt-4 mt-1">Gatuadress</label>
      <input
        placeholder="Gatuadress"
        aria-label="Gatuadress"
        type="text"
        id="streetaddress"
        name="streetaddress"
        required
        v-model="formData.streetaddress"
        class="block drop-shadow-sm rounded-md bg-white pl-1 pr-1"
      />
      <label for="dueDate" class="block mt-4 mt-1">Faktura förfallodag</label>
      <input
        aria-label="Förfallodag"
        type="number"
        id="dueDate"
        name="dueDate"
        required
        v-model="formData.invoice_due_date"
        min="1"
        max="28"
        class="block drop-shadow-sm rounded-md bg-white mt-1 pl-1 pr-1"
      />
      <label for="sendDate" class="block mt-4 mt-1">Faktureringsdag</label>
      <input
        aria-label="Faktureringsdag"
        type="number"
        id="sendDate"
        required
        name="sendDate"
        v-model="formData.invoice_send_date"
        min="1"
        max="28"
        class="block drop-shadow-sm rounded-md bg-white mt-1 pl-1 pr-1"
      />
      <label for="autoInvoice" class="block mt-4 mt-1">Auto-fakturering</label>
      <input
        aria-label="Auto-fakturering"
        type="checkbox"
        id="autoInvoice"
        name="autoInvoice"
        v-model="formData.autoinvoice"
        class="block drop-shadow-sm rounded-md bg-white mt-1"
      />
      <label for="paymentMethod" class="block mt-4 mt-1">Betalningsmetod</label>
      <input
        aria-label="Betalningsmetod"
        placeholder="Betalningsmetod"
        type="text"
        id="paymentMethod"
        name="paymentMethod"
        required
        v-model="formData.payment_method"
        class="block drop-shadow-sm rounded-md bg-white pl-1 pr-1"
      />
      <label for="paymentNumber" class="block mt-4 mt-1">Betalningsnummer</label>
      <input
        aria-label="Betalningsnummer"
        placeholder="Betalningsnummer"
        type="text"
        id="paymentNumber"
        name="paymentNumber"
        required
        v-model="formData.payment_number"
        class="block drop-shadow-sm rounded-md bg-white pl-1 pr-1"
      />
      <div class="w-fit mx-auto">
        <BlueButton v-bind:buttonName="isNew ? 'Skapa' : 'Uppdatera'" type="submit" class="m-3" />
        <OrangeButton
          v-if="!isNew"
          buttonName="Ta bort"
          type="button"
          @clicked="deleteData"
          class="m-3"
        />
        <SlateButton buttonName="Avbryt" type="button" @clicked="abort" class="m-3" />
      </div>
      <p v-if="errorMessage">{{ errorMessage }}</p>
    </form>
  </div>
</template>

<script lang="ts">
import type { RealEstate } from '@/types/RealEstate.types'
import BlueButton from './BlueButton.vue'
import OrangeButton from './OrangeButton.vue'
import {
  deleteRealEstates,
  patchRealEstates,
  postRealEstates,
} from '@/apiService/RealEstateService'
import SlateButton from './SlateButton.vue'

//formulär för fastigheter
export default {
  name: 'FormRealEstate',
  props: ['editRealEstateData', 'isNew'],
  emits: ['submitForm', 'abort', 'getAccommodations', 'getRealEstates'],
  components: {
    BlueButton,
    OrangeButton,
    SlateButton,
  },

  //Meddelande, formulärdata
  data() {
    return {
      errorMessage: '',
      formData: {
        id: this.editRealEstateData.id,
        title: this.editRealEstateData.title,
        streetaddress: this.editRealEstateData.streetaddress,
        invoice_due_date: this.editRealEstateData.invoice_due_date,
        invoice_send_date: this.editRealEstateData.invoice_send_date,
        autoinvoice: this.editRealEstateData.autoinvoice === '1', // Omvandlar till boolean
        payment_number: this.editRealEstateData.payment_number,
        payment_method: this.editRealEstateData.payment_method,
      },
    }
  },

  //Watcher för att hantera vad som händer när propen av typen editRealEstateData uppdateras
  watch: {
    editRealEstateData: {
      handler(newValue) {
        this.formData.id = newValue.id
        this.formData.title = newValue.title
        this.formData.streetaddress = newValue.streetaddress
        this.formData.invoice_due_date = newValue.invoice_due_date
        this.formData.autoinvoice = newValue.autoinvoice === '1'
        this.formData.invoice_send_date = newValue.invoice_send_date
        this.formData.payment_number = newValue.payment_number
        this.formData.payment_method = newValue.payment_method
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    //metod för att avbryta redigering av befintlig fastighet. rensar formulär mm.
    abort() {
      this.$emit('abort')
    },

    //Skickar en post eller patch med fastighetsobjekt beroende på om det är ett nytt eller ej
    async submit() {
      let postRe
      if (this.isNew) {
        postRe = await postRealEstates(this.formData as RealEstate)
      } else {
        postRe = await patchRealEstates(this.formData as RealEstate)
      }
      this.fetchResponse(postRe)
    },

    //Tar bort en fastighet. OBS!! säkerhet ska byggas in i backend och frontend för var som händer vid borttagning mm.
    async deleteData() {
      if (
        confirm(
          'Är du säker på att du vill ta bort felanmälan? \n Borttagen data går ej att återställa',
        ) == true
      ) {
        let deleteRe = await deleteRealEstates(this.formData.id)
        this.fetchResponse(deleteRe)
      }
    },

    //Om svaret är sant hämtas och uppdateras data annars skrivs ett felmeddelande ut
    fetchResponse(check: any) {
      if (check) {
        this.$emit('abort')
        this.$emit('getRealEstates')
        this.$emit('getAccommodations')
      } else {
        this.errorMessage = 'Ändringen gick inte att utföra'
      }
    },
  },
}
</script>
