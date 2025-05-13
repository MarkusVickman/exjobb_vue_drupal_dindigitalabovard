import axios from 'axios'
import type { Invoice } from '@/types/Invoice.types'
import type { RealEstate } from '@/types/RealEstate.types'
import type { Accommodation } from '@/types/Accommodation.types'
import router from '@/router'

// GET-anrop för att hämta alla fakturor
export function getAPIInvoice(): Promise<Invoice[]> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .get('https://www.markuswebb.se/theproject/web/api/invoice/ok', {
        headers: headers,
      })
      .then((response) => {
        // Omvandla varje faktura i responsen
        const invoices = response.data.map((invoice: any) => {
          // Omvandla created timestamp till datumformat
          const createdDate = new Date(parseInt(invoice.created, 10) * 1000)
          // Formatera datumet som YYYY-MM-DD. Använd toISOString och dela på 'T'
          const formattedDate = createdDate.toISOString().split('T')[0]
          // Ersätt timestamp med det formaterade datumet
          return {
            ...invoice,
            created: formattedDate,
          }
        })
        // Returnera den nya listan med fakturor
        return invoices
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

// POST-anrop för att skapa en faktura
export function postInvoice(accommodation: Accommodation): Promise<Invoice> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .post(
        `https://www.markuswebb.se/theproject/web/api/invoice/${accommodation.id}`,
        {},
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

// POST-anrop för att skicka en fakturapåminnelse
export function postInvoiceReminder(invoice_id: string): Promise<Invoice> {
  console.log(invoice_id)
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .post(
        `https://www.markuswebb.se/theproject/web/api/resend_invoice/${invoice_id}`,
        {},
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

// PATCH-anrop för att redigera statusen på en faktura
export function patchInvoice(id: string, status: string): Promise<Invoice> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .patch(
        `https://www.markuswebb.se/theproject/web/api/invoice/${id}`,
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

//DELETE-anrop för att ta bort en faktura
export function deleteInvoice(id: string): Promise<Invoice> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .delete(
        `https://www.markuswebb.se/theproject/web/api/invoice/${id}`,

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
