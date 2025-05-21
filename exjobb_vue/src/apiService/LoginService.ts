import router from '@/router'
import axios from 'axios'

// POST-anrop för att logga in en användare
export function login(username: string, password: string): Promise<string> {
  return (
    axios
      .post(
        'https://www.markuswebb.se/theproject/web/oauth/token',
        {
          client_id: import.meta.env.VITE_CLIENT_ID,
          grant_type: import.meta.env.VITE_GRANT_TYPE,
          client_secret: import.meta.env.VITE_CLIENT_SECRET,
          username: username,
          password: password,
        },
        {
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
        },
      )
      .then((response) => {
        //JWT till storage
        localStorage.setItem('access_token', response.data.access_token)
        router.push('/realestate')
      })

      //Vid fel skrivs felet ut i konsollen samt skriver ut ett meddelande till skärmen
      .catch((error) => {
        console.log(error)
        return error.response.data.message
      })
  )
}

//Funktion för att logga ut en användare. Eventuellt bör en logout token användas och anrop till backend
export function logout() {
  localStorage.removeItem('access_token')
  router.push('/login')
}

// POST-anrop för att registrera en ny användare
export function register(username: string, email: string): Promise<string> {
  return (
    axios
      .post('https://www.markuswebb.se/theproject/web/user/register?_format=json', {
        name: {
          value: username,
        },
        mail: {
          value: email,
        },
      })
      .then((response) => {
        return 'Lyckad registrering. Logga in på din epost för att verifiera användare och välja lösenord.'
      })

      //Vid fel skrivs felet ut i konsollen samt skriver ut ett meddelande till skärmen
      .catch((error) => {
        console.log(error)
        return error.response.data.message
      })
  )
}
