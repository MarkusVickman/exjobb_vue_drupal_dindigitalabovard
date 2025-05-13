<!--modalKomponent för att visa enskild faktura samt ändra dess status-->
<template>
  <div
    class="fixed align-center m-auto z-4 hidden lg:fixed lg:w-4xl lg:h-fit lg:max-h-full max-h-full h-full overflow-scroll w-full inset-0 bg-zinc-100 shadow-sm rounded-3xl flex items-center justify-center z-50 p-10"
    id="modalInvoice"
  >
    <div v-html="formData.invoice_html" class="mb-12"></div>
    <div class="w-fit mx-auto">
      <BlueButton buttonName="Ladda ner" class="m-3" @clicked="printToPdf" />
      <SlateButton buttonName="Skicka påminnelse" class="m-3" @clicked="sendInvoice" />
      <OrangeButton buttonName="Radera" class="m-3" @clicked="removeInvoice" />
    </div>
    <div class="w-fit mx-auto">
      <form @submit.prevent="update()">
        <div class="w-fit">
          <label for="realEstate" class="block mt-4 font-bold">Ändra status</label>
          <select
            aria-label="Ändra status"
            v-model="formData.status"
            required
            class="drop-shadow-sm w-50 bg-white rounded-md p-1"
            id="realEstate"
          >
            <option value="" disabled selected hidden>Välj status</option>
            <option value="payed">Betald</option>
            <option value="not_payed">Ej betald</option>
            <option value="reminded">Påmind</option>
            <option value="discarded">Makulerad</option>
          </select>
        </div>
        <div class="w-fit mx-auto">
          <BlueButton buttonName="Ändra" class="m-3" />
        </div>
        <p class="relative top-4 text-center" v-if="message">{{ message }}</p>
        <div class="w-fit mx-auto">
          <CrossButton class="mt-6" type="button" @clicked="$emit('closeModal')" />
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import BlueButton from './BlueButton.vue'
import OrangeButton from './OrangeButton.vue'
import SlateButton from './SlateButton.vue'
import CrossButton from './CrossButton.vue'
import { deleteInvoice, patchInvoice, postInvoiceReminder } from '@/apiService/InvoiceService'
import html2pdf from 'html2pdf.js'

//invoice komponent med props och emit.
export default {
  name: 'InvoiceModal',
  props: ['invoice'],
  emits: ['closeModal', 'getInvoices'],

  components: {
    BlueButton,
    SlateButton,
    OrangeButton,
    CrossButton,
  },

  //Meddelande, status och formulärdata
  data() {
    return {
      message: '',
      formData: {
        id: this.invoice.id,
        invoice_number: this.invoice.invoice_number,
        tenant_name: this.invoice.tenant_name,
        title: this.invoice.title,
        status: this.invoice.status,
        invoice_html: this.invoice.invoice_html,
        created: this.invoice.created,
        email: this.email,
        accommodationId: this.invoice.accommodationId,
      },
    }
  },

  //Watcher för att hantera när ny prop data ['invoice'] tas in i konponenten
  watch: {
    invoice: {
      handler(newValue) {
        this.message = ''
        this.formData.id = newValue.id
        this.formData.invoice_number = newValue.invoice_number
        this.formData.tenant_name = newValue.tenant_name
        this.formData.title = newValue.title
        this.formData.status = newValue.status
        this.formData.invoice_html = newValue.invoice_html
        this.formData.created = newValue.created
        this.formData.email = newValue.email
        this.formData.accommodationId = newValue.accommodationId
      },
      deep: true,
      immediate: true,
    },
  },

  methods: {
    //Metod för att att skicka iväg ny faktura status och hämta ny den editerade datan (all data)
    async update() {
      const check = await patchInvoice(this.formData.id, this.formData.status)

      if (check) {
        this.$emit('getInvoices')
        this.message = 'Status uppdaterad'
      } else {
        this.message = 'Fel: statusen kunde inte uppdateras'
      }
    },

    //Metod för att ta bort en faktura
    async removeInvoice() {
      if (
        confirm(
          'Är du säker på att du vill ta bort fakturan? \n Borttagen data går ej att återställa',
        ) == true
      ) {
        await deleteInvoice(this.formData.id)
        this.$emit('getInvoices')
        this.$emit('closeModal')
      }
    },

    //Metod som skriver ut html till pdf som användaren du laddar ner.
    printToPdf() {
      //inställningar enligt dokumentationen
      const options = {
        margin: 1,
        filename: `${this.formData.title}_${this.formData.created}.pdf`,
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
      }

      //om ändringar görs här behöver modsvarande deklareras i html2pdf.d.ts göras
      html2pdf().from(this.formData.invoice_html).set(options).save()
    },

    //Skickar en påminnelse faktura. OBS!! EJ KLAR!
    async sendInvoice() {
      const check = await postInvoiceReminder(this.formData.id)

      if (check) {
        this.message = 'En fakturapåminnelse är skickas och statusen ändrades för fakturan.'
        this.$emit('getInvoices')
      } else {
        this.message = 'Fakturapåminnelsen gick inte att skicka.'
      }
    },
  },
}
</script>
