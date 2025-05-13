<!--Komponent med formulär för boende att skapa en ny felrapport-->
<template>
  <div>
    <h2 class="text-2xl underline mt-12 text-center">Meddelande till bovärden</h2>
    <form @submit.prevent="submit" class="w-fit mx-auto p-6">
      <label for="title" class="block mt-4">Ämne</label>
      <input
        placeholder="Ämne"
        type="text"
        id="title"
        name="title"
        required
        v-model="errorReport.title"
        class="block drop-shadow-sm rounded-md bg-white mt-1 lg:w-100 w-70 p-1"
      />
      <label for="information" class="block mt-4">Meddelande</label>
      <textarea
        placeholder="Meddelande"
        id="information"
        name="information"
        required
        v-model="errorReport.message"
        class="block drop-shadow-sm rounded-md bg-white lg:w-100 w-70 h-50 p-1"
      />
      <label for="realEstate" class="block mt-4">Ärende</label>
      <select
        id="realEstate"
        v-model="errorReport.messagetype"
        required
        class="block drop-shadow-sm lg:w-100 w-70 bg-white rounded-md p-1"
      >
        <option value="" disabled selected hidden>Välj ärendetyp</option>
        <option value="Felanmälan">Felanmälan</option>
        <option value="Övrigt">Övrigt</option>
      </select>
      <label for="accommodation" class="block mt-2">Välj boende</label>
      <!--Select som dynamiskt skriver ut alla bostäder där den boende är registrerad-->
      <select
        id="accommodation"
        required
        class="block drop-shadow-sm lg:w-100 w-70 bg-white rounded-md p-1"
        v-model="errorReport.accommodation_ref"
      >
        <option value="" disabled selected hidden>Välj boende</option>
        <option
          v-for="(accommodation, index) in accommodations"
          :key="index"
          :value="accommodation.id"
        >
          {{ accommodation.title }}
        </option>
      </select>
      <div class="w-fit mx-auto">
        <BlueButton type="submit" buttonName="Skicka" class="m-3" />
      </div>
      <p v-if="errorMessage">{{ errorMessage }}</p>
    </form>
  </div>
</template>

<script lang="ts">
import BlueButton from '@/components/BlueButton.vue'
import OrangeButton from './OrangeButton.vue'
import type { ErrorReport } from '@/types/ErrorReport.types'
import { postErrorReport } from '@/apiService/TenantService'

export default {
  name: 'FormErrorReport',
  props: ['email', 'accommodations'],
  emits: ['submitForm', 'abort'],
  components: {
    BlueButton,
    OrangeButton,
  },

  //felrapportsdata samt variabel för meddelande.
  data() {
    return {
      errorMessage: '',
      errorReport: {
        title: '',
        message: '',
        messagetype: '',
        email: this.email,
        accommodation_ref: '',
      },
    }
  },

  methods: {
    //metod för att rensa formuläret
    abort() {
      this.$emit('abort')
    },

    //metod för att skicka in en ny felanmälan
    async submit() {
      const postRe = await postErrorReport(this.errorReport as ErrorReport)
      this.fetchResponse(postRe)
    },

    //Kontrollerar om felrapportens svar från backend och skriver ut fel eller uppdaterar data OBS!! Används ej i nuläget!
    fetchResponse(check: any) {
      if (check) {
        this.$emit('abort')
        this.errorMessage = 'Meddelande skickat'
        this.errorReport.title = ''
        this.errorReport.message = ''
        this.errorReport.messagetype = ''
        this.errorReport.accommodation_ref = ''
      } else {
        this.errorMessage = 'Fel: Meddelandet gick inte att skicka'
      }
    },
  },
}
</script>
