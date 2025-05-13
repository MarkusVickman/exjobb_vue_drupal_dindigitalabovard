<!--Vy för information till boende-->
<template>
  <section class="max-w-5xl mx-auto">
    <div class="p-2">
      <h1 class="text-3xl text-center mt-4 mb-4">Informera</h1>
      <p>
        Information till boende i dina fastigheter. Välj berörd fastighet, skriv titel på inlägget
        och den information som du vill nå ut med. Boende kommer åt informationen med den
        e-postadress som är registrerad på respektive lägenhet.
      </p>
      <p class="mt-1">Obs. dela ingen känslig information här.</p>
    </div>
    <!--Formulärkomponent för att fylla i information till sida fastigheter -->
    <FormInformation
      class="align-center m-auto"
      @abort="emptyForm"
      :editInformationData="editInformationData"
      @getInformation="getInformation"
      :isNew="isNew"
      :realEstates="realEstates"
    />
  </section>
  <section v-if="information" class="w-fit mx-auto">
    <h2 class="text-2xl underline mt-12 text-center">Aktiv information</h2>

    <!--För varje fastighet skrivers relaterad fastighetsinformation ut-->
    <div v-for="(realEstate, index) in realEstates" :key="index">
      <!--Skriver endast ut containern om information finns, kontrolleras med metoden filteredInformation-->
      <LargeContainer
        :title="realEstate.title"
        v-if="filteredInformation(realEstate.id).length > 0"
      >
        <!--Skriver ut information för respektive fastighet-->
        <div v-for="(info, index) in information" :key="index">
          <TextContentContainer
            v-if="info.realestate_id[0].target_id === realEstate.id"
            :id="info.id"
            :title="info.title"
            :text1="info.information"
            :text2="info.created"
            text2Header="Skapad: "
            class="mb-6 min-h-36"
          >
            <!--knappar för att ta bort eller ändra information-->
            <div class="w-fit mx-auto">
              <BlueButton buttonName="Redigera" @clicked="editInformation(info)" class="m-3" />
              <OrangeButton buttonName="Ta bort" @clicked="deleteData(info.id)" class="m-3" />
            </div>
          </TextContentContainer>
        </div>
      </LargeContainer>
    </div>
  </section>
</template>

<script lang="ts">
import BlueButton from '@/components/BlueButton.vue'
import LargeContainer from '@/components/LargeContainer.vue'
import TextContentContainer from '@/components/TextContentContainer.vue'
import type { RealEstate } from '@/types/RealEstate.types'
import { getAPIRealEstates } from '@/apiService/RealEstateService'
import FormInformation from '@/components/FormInformation.vue'
import type { Information } from '@/types/Information.types'
import { deleteInformation, getAPIInformation } from '@/apiService/InformationService'
import OrangeButton from '@/components/OrangeButton.vue'

export default {
  name: 'InformationView',
  components: {
    BlueButton,
    OrangeButton,
    FormInformation,
    LargeContainer,
    TextContentContainer,
  },

  //Dataarrayer samt ett object med den information som ska ändras i formuläret
  data() {
    return {
      realEstates: [] as RealEstate[],
      information: [] as Information[],
      editInformationData: {} as Information,
      isNew: true,
    }
  },

  //Vid vyladdning
  mounted() {
    //hämtar information för fastigheter och relaterad information
    this.getRealEstates()
    this.getInformation()
  },
  methods: {
    //hämtar fastighets information
    async getInformation() {
      this.information = await getAPIInformation()
    },

    //Hämtar fastigheter
    async getRealEstates() {
      this.realEstates = await getAPIRealEstates()
    },

    //Returnerar objektet med information om realestate_id matchar
    filteredInformation(realestate_id: string) {
      return this.information.filter((info) => info.realestate_id[0].target_id === realestate_id)
    },

    //Rensar formulär och återstället bool(för att säkerställa om det är en ny eller om en gammal ska redigeras)
    emptyForm() {
      this.isNew = true
      this.editInformationData = {} as Information
    },

    //metod för att ta bort data. Kontrolleras med en confirm. Borde vara en bättre men..
    async deleteData(id: string) {
      if (confirm('Är du säker på att du vill ta bort informationen?') == true) {
        let deleteRe = await deleteInformation(id)
        this.getInformation()
      }
    },

    //Metod för att fylla formuläret med datan som ska ändras samt JS för att scrolla dit
    editInformation(info: Information) {
      this.editInformationData = info
      window.scrollTo({ top: 0, behavior: 'smooth' })
      this.isNew = false
    },
  },
}
</script>
