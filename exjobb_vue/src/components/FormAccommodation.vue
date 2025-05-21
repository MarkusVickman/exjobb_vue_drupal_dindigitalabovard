<!--Komponent för att ändra eller skapa nytt boende-->
<template>
  <div
   class="absolute overflow-scroll lg:overflow-auto align-center m-auto z-4 hidden lg:absolute lg:w-2xl lg:h-fit h-full w-full inset-0 bg-zinc-100 shadow-sm rounded-3xl flex items-center justify-center z-50 p-10"
  >
    <h2 class="text-2xl underline text-center">Bostadsformulär</h2>
    <form @submit.prevent="submit" class="w-fit mx-auto p-6">
      <input
        type="hidden"
        id="AccommodationId"
        name="AccommodationId"
        class="block drop-shadow-sm rounded-md bg-white"
        v-model="formAcData.id"
      />
      <input
        type="hidden"
        id="RelatedREId"
        name="RelatedREId"
        class="block drop-shadow-sm rounded-md bg-white"
        v-model="formAcData.rEId"
      />
      <label for="Anamn" class="block mt-4 mt-1">Bostadsbeteckning</label>
      <input
        placeholder="Bostadsbeteckning"
        aria-label="Bestadsbeteckning"
        type="text"
        id="Anamn"
        name="Anamn"
        required
        class="block drop-shadow-sm rounded-md bg-white mt-1 pl-1 pr-1"
        v-model="formAcData.title"
      />
      <label for="tenant" class="block mt-4 mt-1">Hyresgäst</label>
      <input
        placeholder="Hyresgäst"
        aria-label="Hyresgäst"
        type="text"
        id="tenant"
        name="tenant"
        class="block drop-shadow-sm rounded-md bg-white mt-1 pl-1 pr-1"
        v-model="formAcData.tenant"
      />
      <label for="email" class="block mt-4 mt-1">Hyresgästens e-postadress</label>
      <input
        placeholder="E-postadress"
        aria-label="E-postadress"
        type="email"
        id="email"
        name="email"
        class="block drop-shadow-sm rounded-md bg-white pl-1 pr-1"
        v-model="formAcData.email"
      />
      <label for="rent" class="block mt-4 mt-1">Månadshyra i kr</label>
      <input
        placeholder="Hyra"
        aria-label="Hyra"
        type="number"
        min="0"
        max="100000"
        id="rent"
        name="rent"
        required
        class="block drop-shadow-sm rounded-md bg-white mt-1 pl-1 pr-1"
        v-model="formAcData.rent"
      />
      <!--knappar för att skicka in formulär, ta bort data eller avbryta ändring-->
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
import {
  deleteAccommodations,
  patchAccommodations,
  postAccommodations,
} from '@/apiService/AccommodationService'
import BlueButton from './BlueButton.vue'
import OrangeButton from './OrangeButton.vue'
import SlateButton from './SlateButton.vue'

//formulär för bostäder
export default {
  name: 'FormAccommodation',
  props: ['editAccommodationData', 'isNew', 'rEId'],
  emits: ['submitForm', 'abort', 'getAccommodations', 'getRealEstates'],
  components: {
    BlueButton,
    OrangeButton,
    SlateButton,
  },

  //Data för felmeddelande och för formulärdata
  data() {
    return {
      errorMessage: '',
      formAcData: {
        id: this.editAccommodationData.id,
        title: this.editAccommodationData.title,
        email: this.editAccommodationData.emailaddress,
        rent: this.editAccommodationData.rent,
        tenant: this.editAccommodationData.tenant,
        rEId: this.rEId,
      },
    }
  },

  //Watcher för vad som ska ändras när prop editAccommodationsData ändras
  watch: {
    editAccommodationData: {
      handler(newValue) {
        this.formAcData.id = newValue.id
        this.formAcData.title = newValue.title
        this.formAcData.email = newValue.emailaddress
        this.formAcData.rent = newValue.rent
        this.formAcData.tenant = newValue.tenant
        this.formAcData.rEId = this.rEId
      },
      deep: true,
      immediate: true,
    },
  },

  //vid laddning
  mounted() {},

  methods: {
    //metod som rensar formulärdata
    abort() {
      this.$emit('abort')
    },

    //Skickar post eller patch beroende på om datan är ny eller redigering
    async submit() {
      let postAc
      if (this.isNew) {
        postAc = await postAccommodations(this.formAcData as any)
      } else {
        postAc = await patchAccommodations(this.formAcData as any)
      }
      this.fetchResponse(postAc)
    },

    //Tar bort data OBS!! måst byggas säkrare då det finns annan relaterad data.
    async deleteData() {
      if (
        confirm(
          'Är du säker på att du vill ta bort felanmälan? \n Borttagen data går ej att återställa',
        ) == true
      ) {
        let deleteAc = await deleteAccommodations(this.formAcData.id)
        this.fetchResponse(deleteAc)
      }
    },

    //Beroende på svara från submit och delete så uppdataras fomulär samt data eller så skrivs ett fel ut
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
