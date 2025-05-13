import axios from 'axios'
import type { RealEstate } from '@/types/RealEstate.types'
import router from '@/router'

// GET-anrop för att hämta alla fastigheter
export function getAPIRealEstates(): Promise<RealEstate[]> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .get('https://www.markuswebb.se/theproject/web/api/realestate/ok', {
        headers: headers,
      })
      .then((response) => {
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
        return error.response.data.message
      })
  )
}

// POST-anrop för att skapa en fastighet
export function postRealEstates(realestate: RealEstate): Promise<RealEstate> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .post(
        'https://www.markuswebb.se/theproject/web/api/realestate',
        {
          title: realestate.title,
          streetaddress: realestate.streetaddress,
          autoinvoice: realestate.autoinvoice,
          invoice_due_date: realestate.invoice_due_date,
          invoice_send_date: realestate.invoice_send_date,
          payment_method: realestate.payment_method,
          payment_number: realestate.payment_number,
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

// PATCH-anrop för att redigera fastighet
export function patchRealEstates(realestate: RealEstate): Promise<RealEstate> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .patch(
        `https://www.markuswebb.se/theproject/web/api/realestate/${realestate.id}`,
        {
          title: realestate.title,
          streetaddress: realestate.streetaddress,
          autoinvoice: realestate.autoinvoice,
          invoice_due_date: realestate.invoice_due_date,
          invoice_send_date: realestate.invoice_send_date,
          payment_method: realestate.payment_method,
          payment_number: realestate.payment_number,
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

// DELETE-anrop för att ta bort fastighet
export function deleteRealEstates(id: string): Promise<RealEstate> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  //Post request
  return (
    axios
      .delete(
        `https://www.markuswebb.se/theproject/web/api/realestate/${id}`,

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
