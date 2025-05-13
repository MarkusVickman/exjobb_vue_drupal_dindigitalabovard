import axios from 'axios'
import type { Accommodation } from '@/types/Accommodation.types'
import router from '@/router'

// GET-anrop för att hämta alla bostäder
export function getAPIAccommodations(): Promise<Accommodation[]> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .get('https://www.markuswebb.se/theproject/web/api/accommodation/ok', {
        headers: headers,
      })
      .then((response) => {
        return response.data
      })

      //Vid fel skrivs felet ut i konsollen användaren loggas ut och dirigeras till login
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

// POST-anrop för att skapa en bostad
export function postAccommodations(accommodation: any): Promise<Accommodation> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .post(
        'https://www.markuswebb.se/theproject/web/api/accommodation',
        {
          title: accommodation.title,
          emailaddress: accommodation.email,
          rent: accommodation.rent,
          tenant: accommodation.tenant,
          real_estate_id: accommodation.rEId,
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

      //Vid fel skrivs felet ut i konsollen användaren loggas
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

// PATCH-anrop för att redigera en bostad
export function patchAccommodations(accommodation: any): Promise<Accommodation> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .patch(
        `https://www.markuswebb.se/theproject/web/api/accommodation/${accommodation.id}`,
        {
          title: accommodation.title,
          emailaddress: accommodation.email,
          rent: accommodation.rent,
          tenant: accommodation.tenant,
          real_estate_id: accommodation.rEId,
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

// DELETE-anrop för att ta bort en bostad
export function deleteAccommodations(id: string): Promise<Accommodation> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .delete(
        `https://www.markuswebb.se/theproject/web/api/accommodation/${id}`,

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
