import axios from 'axios'
import type { Accommodation } from '@/types/Accommodation.types'
import router from '@/router'
import type { ErrorReport } from '@/types/ErrorReport.types'

// GET-anrop för att hämta alla felrapporter
export function getAPIErrorReports(): Promise<ErrorReport[]> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .get('https://www.markuswebb.se/theproject/web/api/error_report/id', {
        headers: headers,
      })
      .then((response) => {
        // Omvandla varje felrapport i responsen
        const errorReports = response.data.map((errorReport: any) => {
          // Omvandla created timestamp till datumformat
          const createdDate = new Date(parseInt(errorReport.created, 10) * 1000)
          // Formatera datumet som YYYY-MM-DD. Använd toISOString och dela på 'T'
          const formattedDate = createdDate.toISOString().split('T')[0]
          // Ersätt timestamp med det formaterade datumet
          return {
            ...errorReport,
            created: formattedDate,
          }
        })
        // Returnera den nya listan med felrapporter
        return errorReports
      })

      //Vid fel skrivs felet ut i konsollen
      .catch((error) => {
        console.log(error)
        //Status 401 indikerar att token är ogiltig och användaren loggas ut.
        if (error.status === 401) {
          localStorage.removeItem('access_token')
          router.push('/login')
        }
        return error.response.data.message
      })
  )
}

// PATCH-anrop för att ändra status för en felrapport
export function patchErrorReport(id: string, status: string): Promise<ErrorReport> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .patch(
        `https://www.markuswebb.se/theproject/web/api/error_report/${id}`,
        {
          status: status,
        },
        {
          headers: headers,
        },
      )
      .then((response) => {
        if (response.status === 201) {
          return true
        }
        return response.data
      })

      //Vid fel skrivs felet ut i konsollen
      .catch((error) => {
        console.log(error)
        //Status 401 indikerar att token är ogiltig och användaren loggas ut.
        if (error.status === 401) {
          localStorage.removeItem('access_token')
          router.push('/login')
        }
        return false
      })
  )
}

// DELETE-anrop för att ändra status för en felrapport
export function deleteErrorReport(id: string): Promise<ErrorReport> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  //Post request
  return (
    axios
      .delete(
        `https://www.markuswebb.se/theproject/web/api/error_report/${id}`,

        {
          headers: headers,
        },
      )
      .then((response) => {
        if (response.status === 204) {
          return true
        }
        return response.data
      })

      //Vid fel skrivs felet ut i konsollen
      .catch((error) => {
        console.log(error)
        //Status 401 indikerar att token är ogiltig och användaren loggas ut.
        if (error.status === 401) {
          localStorage.removeItem('access_token')
          router.push('/login')
        }
        return false
      })
  )
}
