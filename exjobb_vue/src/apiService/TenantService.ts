import axios from 'axios'
import type { Information } from '@/types/Information.types'
import type { ErrorReport } from '@/types/ErrorReport.types'
import type { RealEstateId } from '@/types/RealEstate.types'

// POST-anrop för att hämta all information till de fastigheter som emailen är registrerad för
export function getTenantInformation(realEstateId: string, email: string): Promise<Information[]> {
  return (
    axios
      .post(`https://www.markuswebb.se/theproject/web/api/tenant`, {
        realEstateId: realEstateId,
        email: email,
      })
      .then((response) => {
        // Omvandla varje informationsobjekt i responsen
        const informations = response.data.map((information: any) => {
          // Omvandla created timestamp till datumformat
          const createdDate = new Date(parseInt(information.created, 10) * 1000)
          // Formatera datumet som YYYY-MM-DD. Använd toISOString och dela på 'T'
          const formattedDate = createdDate.toISOString().split('T')[0]
          // Ersätt timestamp med det formaterade datumet
          return {
            ...information,
            created: formattedDate,
          }
        })
        // lägger till den nya listan promise
        response.data = informations
        // Returnera response
        return response
      })

      //Vid fel skrivs felet ut i konsollen
      .catch((error) => {
        console.log(error)
        return error.response
      })
  )
}

//POST-anrop som tar emot en felanmälan från en boende
export function postErrorReport(errorReport: ErrorReport): Promise<ErrorReport> {
  return (
    axios
      .post('https://www.markuswebb.se/theproject/web/api/error_report', {
        title: errorReport.title,
        message: errorReport.message,
        messagetype: errorReport.messagetype,
        accommodation_ref: errorReport.accommodation_ref,
        emailaddress: errorReport.email,
      })
      .then((response) => {
        if (response.status === 201) {
          return true
        }
        return response.data
      })

      //Vid fel skrivs felet ut i konsollen
      .catch((error) => {
        console.log(error)
        return false
      })
  )
}

//GET-anrop för att hämta en lista med alla fastigheter. Dessa kan användaren välja sin bovärd ifrån på sidan TenantView.vue
export function getRealEstatesList(): Promise<RealEstateId[]> {
  return (
    axios
      .get(`https://www.markuswebb.se/theproject/web/api/realestate_list`, {})
      .then((response) => {
        return response.data
      })

      //Vid fel skrivs felet ut i konsollen användaren loggas ut och dirigeras till login
      .catch((error) => {
        console.log(error)
        return false
      })
  )
}

//GET-anrop för att hämta en lista med alla bostäder. Dessa kan användaren välja sin bovärd ifrån på sidan TenantView.vue
export function getAccommodationsList(email: string): Promise<RealEstateId[]> {
  return (
    axios
      .get(`https://www.markuswebb.se/theproject/web/api/tenant/${email}`, {})
      .then((response) => {
        return response.data
      })

      //Vid fel skrivs felet ut i konsollen användaren loggas ut och dirigeras till login
      .catch((error) => {
        console.log(error)
        return false
      })
  )
}
