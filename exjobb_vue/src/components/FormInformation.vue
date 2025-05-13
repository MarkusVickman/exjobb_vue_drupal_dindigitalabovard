<!--Komponent med formulär för att skapa information till boende-->
<template>
  <div>
    <h2 class="text-2xl underline mt-12 text-center">Information till fastighet</h2>
    <form @submit.prevent="submit" class="w-fit mx-auto p-6">
      <input
        type="hidden"
        id="RealEstateId"
        name="RealEstateId"
        v-model="formData.id"
        class="block drop-shadow-sm rounded-md bg-white"
      />
      <label for="title" class="block mt-4">Titel</label>
      <input
        aria-label="Titel"
        placeholder="Titel"
        type="text"
        id="title"
        name="title"
        required
        v-model="formData.title"
        class="block drop-shadow-sm rounded-md bg-white mt-1 lg:w-100 w-70 p-1"
      />
      <label for="information" class="block mt-4">Information</label>
      <textarea
        aria-label="Information"
        placeholder="Information"
        id="information"
        name="information"
        required
        v-model="formData.information"
        class="block drop-shadow-sm rounded-md bg-white lg:w-100 w-70 h-50 p-1"
      />
      <!--Input eller select visas beroende på om ett nytt inlägg ska skapas eller ett gammalt ska redigeras-->
      <label for="realEstate" class="block mt-4">Fastighet</label>
      <input
        disabled
        id="realEstate"
        name="realEstate"
        v-if="!isNew"
        v-model="formData.realestate_id[0].target_id"
      />
      <!--dynamiska alternativ för alla relaterade fastigheter-->
      <select
        aria-label="Fastighet"
        v-if="isNew"
        id="realEstate"
        name="realEstate"
        v-model="formData.realestate_id"
        required
        class="block drop-shadow-sm lg:w-100 w-70 bg-white rounded-md p-1"
      >
        <option value="" disabled selected>Välj fastighet</option>
        <option v-for="(realEstate, index) in realEstates" :key="index" :value="realEstate.id">
          {{ realEstate.title }}
        </option>
      </select>

      <!-- knappar för att skicka eller rensa formulär -->
      <div class="w-fit mx-auto">
        <BlueButton v-bind:buttonName="isNew ? 'Skapa' : 'Uppdatera'" type="submit" class="m-3" />

        <OrangeButton buttonName="Avbryt" type="button" @clicked="abort" class="m-3" />
      </div>
      <p v-if="errorMessage">{{ errorMessage }}</p>
    </form>
  </div>
</template>

<script lang="ts">
import { patchInformation, postInformation } from '@/apiService/InformationService'
import BlueButton from '@/components/BlueButton.vue'
import OrangeButton from './OrangeButton.vue'
import type { Information } from '@/types/Information.types'

export default {
  name: 'FormInformation',
  props: ['editInformationData', 'isNew', 'realEstates'],
  emits: ['submitForm', 'abort', 'getInformation'],
  components: {
    BlueButton,
    OrangeButton,
  },

  //formulärdata samt variabel för meddelande.
  data() {
    return {
      errorMessage: '',
      formData: {
        id: this.editInformationData.id,
        realestate_id: this.editInformationData.realestate_id,
        title: this.editInformationData.title,
        information: this.editInformationData.information,
      },
    }
  },

  //Watcher för vad som ska göras om editInformationData (prop) ändras
  watch: {
    editInformationData: {
      handler(newValue) {
        this.formData.id = newValue.id
        this.formData.realestate_id = newValue.realestate_id
        this.formData.title = newValue.title
        this.formData.information = newValue.information
      },
      deep: true,
      immediate: true,
    },
  },

  //Vid vyladdning
  mounted() {},

  methods: {
    //Rensar formulärdata
    abort() {
      this.$emit('abort')
    },

    //skickar post eller patch beroende på om det är ny eller data som ska ändras
    async submit() {
      // Här kan du använda formData för att skicka till API
      let postRe
      if (this.isNew) {
        postRe = await postInformation(this.formData as Information)
      } else {
        postRe = await patchInformation(this.formData as Information)
      }
      this.fetchResponse(postRe)
    },

    //Beroende på svara från backend uppdateras data eller så skrivs ett felmeddelande ut
    fetchResponse(check: any) {
      if (check) {
        this.$emit('abort')
        this.$emit('getInformation')
      } else {
        this.errorMessage = 'Ändringen gick inte att utföra'
      }
    },
  },
}
</script>
