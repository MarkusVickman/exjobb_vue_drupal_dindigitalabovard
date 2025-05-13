<!--Vy för boende/hyresgäst-->
<template>
  <section class="max-w-5xl mx-auto">
    <div class="p-2">
      <h1 class="text-3xl text-center mt-4 mb-4">Din Bovärd</h1>
      <p>
        På denna sida kan din bovärd lägga upp information. Det är även här du ska skicka in
        felanmälningen eller meddelande till bovärden vid behov. Logga in med den Epost-adress som
        du angett till din bovärd. Vid problem kontakta din bovärd.
      </p>
    </div>

    <!--Formulär där en boende fyller i email och fastighet, dessa kontrolleras om email finns registrerad på någon av boenden relaterade till den angivna fastigheten-->
    <div class="mt-12">
      <form
        @submit.prevent="submit"
        class="w-fit mx-auto bg-zinc-100 p-12 shadow-sm rounded-3xl"
        v-if="!isRegistered"
      >
        <h1 class="text-3xl text-center">Logga in</h1>
        <label for="email" class="block mt-2">E-postadress</label>
        <input
          placeholder="E-postadress"
          aria-label="E-postadress"
          required
          type="email"
          id="email"
          name="email"
          class="block drop-shadow-sm rounded-md bg-white pl-1 pr-1 lg:w-100"
          v-model="email"
        />
        <label for="realestate_owner" class="block mt-2">Fastighet</label>
        <!--En select där alla fastigheter listas dynamiskt-->
        <select
          id="realestate_owner"
          required
          class="block drop-shadow-sm lg:w-100 bg-white rounded-md p-1"
          v-model="reId"
          aria-label="Bovärd"
        >
          <option value="" disabled selected hidden>Välj fastighet</option>
          <option v-for="(realEstate, index) in realEstates" :key="index" :value="realEstate.id">
            {{ realEstate.title }}
          </option>
        </select>
        <!--logga in knapp-->
        <BlueButton
          id="submitLogin"
          buttonName="Logga in"
          class="mx-auto mt-6 block"
          type="submit"
        />
      </form>
      <p class="text-center mb-4 mt-4" v-if="errorMessage">{{ errorMessage }}</p>
    </div>
  </section>

  <!-- efter godkännande visas information och formulär för felanmälningar för den boende -->
  <div v-if="isRegistered">
    <!--Formulär för att skicka in felanmälningar-->
    <FormErrorReport class="align-center m-auto" :email="email" :accommodations="accommodations" />
    <section class="w-fit mx-auto" v-if="information">
      <h2 class="text-2xl underline mt-12 text-center">
        Information till {{ information[0].realestate_title }}
      </h2>
      <!--Skriver ut information till den boende-->
      <div v-for="(info, index) in information" :key="index">
        <div v-if="info.title">
          <LargeContainer :title="info.title">
            <TextContentContainer
              :id="info.id"
              :text1="info.information"
              :text6="info.created"
              text6Header="Skapad: "
              class="mb-6 min-h-36"
            >
            </TextContentContainer>
          </LargeContainer>
        </div>
      </div>
    </section>
  </div>
</template>

<script lang="ts">
import BlueButton from '@/components/BlueButton.vue'
import LargeContainer from '@/components/LargeContainer.vue'
import TextContentContainer from '@/components/TextContentContainer.vue'
import type { Information } from '@/types/Information.types'
import OrangeButton from '@/components/OrangeButton.vue'
import FormErrorReport from '@/components/FormErrorReport.vue'
import {
  getAccommodationsList,
  getRealEstatesList,
  getTenantInformation,
} from '@/apiService/TenantService'

export default {
  name: 'TenantView',
  components: {
    BlueButton,
    OrangeButton,
    FormErrorReport,
    LargeContainer,
    TextContentContainer,
  },

  //Dataarray för information,
  data() {
    return {
      information: [] as Information[],
      isRegistered: false,
      errorMessage: '',
      email: '',
      accommodations: [
        {
          id: '',
          title: ',',
        },
      ],
      realEstates: [] as {
        title: string
        id: string
      }[],
      reId: '',
    }
  },

  //Vid app start kontrolleras inloggning OBS!! ska byggas om!
  mounted() {
    //Hämtar lista med fastighets id och namn
    this.ListRealEstates()
  },
  methods: {
    //Hämtar lista med fastighets id och namn
    async ListRealEstates() {
      this.realEstates = await getRealEstatesList()
    },

    //Hämtar lista med bostads id och namn
    async ListAccommodations() {
      this.accommodations = await getAccommodationsList(this.email)
    },

    //skickar ett anrop för att hämta information med inloggningsinformation(email + fastighet)
    async submit() {
      const information = (await getTenantInformation(this.reId, this.email)) as any

      if (information.status == 200 || information.status == 204) {
        this.information = information.data
        this.isRegistered = true
        this.errorMessage = ''

        if (information.status == 200) {
          this.ListAccommodations()
        }
      } else {
        this.errorMessage = information.data.error
      }
    },
  },
}
</script>
