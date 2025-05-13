<!--komponent för visning av felrapport saḿt för ändring av status på den.-->
<template>
  <div
    class="fixed align-center m-auto z-4 hidden lg:fixed lg:w-4xl lg:h-fit lg:max-h-full max-h-full h-full overflow-scroll w-full inset-0 bg-zinc-100 shadow-sm rounded-3xl flex items-center justify-center z-50 p-10"
    id="modalErrorReport"
  >
    <!--Ett formulär som använder mobilutseende av formulär för visning av en felrapport-->
    <div class="w-fit mx-auto">
      <table class="">
        <thead>
          <tr>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">
              Ärendenummer
            </th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">Bostad</th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">E-post</th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">Boende</th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">Titel</th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">
              Meddelande
            </th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">
              Ärendetyp
            </th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">Status</th>
            <th class="hover:cursor-pointer hover:bg-slate-400 bg-slate-200" scope="col">Skapad</th>
            <th scope="col">Ta Bort</th>
          </tr>
        </thead>
        <tbody>
          <tr :class="{ 'line-through': errorReport.status === 'discarded' }">
            <td data-label="Ärendenummer">{{ errorReport.id }}</td>
            <td data-label="Bostad">{{ errorReport.accommodation_ref }}</td>
            <td data-label="E-post">{{ errorReport.email }}</td>
            <td data-label="Boende">{{ errorReport.name }}</td>
            <td data-label="Titel">{{ errorReport.title }}</td>
            <td data-label="Meddelande">{{ errorReport.message }}</td>
            <td data-label="Ärendetyp">{{ errorReport.messagetype }}</td>
            <td
              data-label="Status"
              :class="{
                'bg-green-100': errorReport.status === 'completed',
                'bg-orange-100': errorReport.status === 'started',
                'bg-red-100': errorReport.status === 'not_started',
                'bg-red-0': errorReport.status === 'discarded',
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
            <!--knapp för att ta bort felrapport-->
            <td data-label="Ta bort" class="">
              <OrangeButton buttonName="Radera" class="m-3" @clicked="removeErrorReport" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="w-fit mx-auto">
      <!--formulär för att ändra status på felrapporten-->
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
            <option value="not_started">Ej påbörjad</option>
            <option value="started">Pågående</option>
            <option value="completed">Avklarad</option>
            <option value="discarded">Makulerad</option>
          </select>
        </div>
        <div class="w-fit mx-auto">
          <BlueButton buttonName="Ändra" class="m-3" />
        </div>
      </form>
    </div>
    <p class="relative top-4 left-4" v-if="message">{{ message }}</p>
    <div class="w-fit mx-auto">
      <CrossButton class="mt-6" type="button" @clicked="$emit('closeModal')" />
    </div>
  </div>
</template>

<script lang="ts">
import BlueButton from './BlueButton.vue'
import OrangeButton from './OrangeButton.vue'
import SlateButton from './SlateButton.vue'
import CrossButton from './CrossButton.vue'
import { deleteErrorReport, patchErrorReport } from '@/apiService/ErrorReportService'

//felmeddelande komponent med props och emit.
export default {
  name: 'ErrorReportModal',
  props: ['errorReport'],
  emits: ['closeModal', 'getErrorReports'],

  components: {
    BlueButton,
    SlateButton,
    OrangeButton,
    CrossButton,
  },

  //Data för felmeddelande, status för felrapporten samt formulärdata
  data() {
    return {
      message: '',
      formData: {
        id: this.errorReport.id,
        title: this.errorReport.title,
        message: this.errorReport.message,
        messagetype: this.errorReport.messagetype,
        email: this.errorReport.email,
        name: this.errorReport.name,
        accommodation_ref: this.errorReport.accommodation_ref,
        status: this.errorReport.status,
        created: this.errorReport.created,
      },
    }
  },

  //watcher för vad som ska göras när prop errorReport ändras
  watch: {
    errorReport: {
      handler(newValue) {
        this.message = ''
        this.formData.id = newValue.id
        this.formData.title = newValue.title
        this.formData.message = newValue.message
        this.formData.messagetype = newValue.messagetype
        this.formData.email = newValue.email
        this.formData.name = newValue.name
        this.formData.accommodation_ref = newValue.accommodation_ref
        this.formData.status = newValue.status
        this.formData.created = newValue.created
      },
      deep: true,
      immediate: true,
    },
  },

  methods: {
    //Metod för att uppdatera status av felrapport, skickar patch
    async update() {
      this.message = 'Status uppdaterad'
      await patchErrorReport(this.formData.id, this.formData.status)
      this.$emit('getErrorReports')
    },

    //Tar bort en fel rapport
    async removeErrorReport() {
      if (
        confirm(
          'Är du säker på att du vill ta bort felanmälan? \n Borttagen data går ej att återställa',
        ) == true
      ) {
        await deleteErrorReport(this.formData.id)
        this.$emit('getErrorReports')
        this.$emit('closeModal')
      }
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

@media screen and (max-width: 100000px) {
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
