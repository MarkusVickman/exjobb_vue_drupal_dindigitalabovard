import axios from 'axios'
import type { Information } from '@/types/Information.types'
import router from '@/router'

// GET-anrop för att hämta all information till fastigheter
export function getAPIInformation(): Promise<Information[]> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .get('https://www.markuswebb.se/theproject/web/api/information/ok', {
        headers: headers,
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
        // Returnera den nya listan med informationsobjekt
        return informations
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

// POST-anrop för att skapa information till en bostad
export function postInformation(information: Information): Promise<Information> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .post(
        'https://www.markuswebb.se/theproject/web/api/information',
        {
          realestate_id: information.realestate_id,
          title: information.title,
          information: information.information,
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

// PATCH-anrop för att redigera fastighetsinformation
export function patchInformation(information: Information): Promise<Information> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .patch(
        `https://www.markuswebb.se/theproject/web/api/information/${information.id}`,
        {
          title: information.title,
          information: information.information,
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

// DELETE-anrop för att ta bort information till fastighet
export function deleteInformation(id: string): Promise<Information> {
  //skapar ett objekt till headern med access token
  const headers = { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
  return (
    axios
      .delete(
        `https://www.markuswebb.se/theproject/web/api/information/${id}`,

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
