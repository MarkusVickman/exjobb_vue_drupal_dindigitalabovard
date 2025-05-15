<!--Vy för inloggning, registrering mm.-->
<template>
  <div class="mt-12">
    <!--Formuläret används till både registrering och inloggning.-->
    <form @submit.prevent="submit" class="w-fit mx-auto bg-zinc-100 p-12 shadow-sm rounded-3xl">
      <h1 class="text-3xl text-center">{{ formTitle }}</h1>
      <label for="username" class="block mt-4">Bovärdsnamn</label>
      <input
        v-model="username"
        type="text"
        id="username"
        name="username"
        required
        class="block drop-shadow-sm rounded-md bg-white pl-1 pr-1"
        placeholder="Bovärdsnamn"
      />
      <label for="password" v-if="loginToggle" class="block mt-2">Lösenord</label>
      <input
        v-if="loginToggle"
        v-model="password"
        type="password"
        id="password"
        name="password"
        required
        minlength="8"
        maxlength="20"
        class="block drop-shadow-sm rounded-md bg-white pl-1 pr-1"
        placeholder="Lösenord"
        aria-label="Lösenord"
      />
      <label for="email" v-if="!loginToggle" class="block mt-2">E-postadress</label>
      <input
        v-if="!loginToggle"
        required
        type="email"
        id="email"
        name="email"
        class="block drop-shadow-sm rounded-md bg-white pl-1 pr-1"
        v-model="email"
        placeholder="E-postadress"
        aria-label="E-postadress"
      />
      <BlueButton
        id="submitLogin"
        :buttonName="loginToggle ? 'Logga in' : 'Registrera'"
        class="mx-auto mt-6 block"
        type="submit"
      />
    </form>
    <!--Länkar och knappar för bla. att logga in gäst eller resetta lösenord-->
    <div class="text-center mb-4 mt-4 text-blue-600">
      <RouterLink class="text-blue-600" to="/tenant" aria-label="Inloggning för hyresgäster">Logga in hyresgäst</RouterLink>
    </div>
    <div class="text-center mb-2 mt-2 text-blue-600" v-if="!loginToggle">
      <button @click="toggle" class="cursor-pointer hover:text-blue-800">Logga in</button>
    </div>
    <div class="text-center mb-4 mt-4 text-blue-600" v-if="loginToggle">
      <button @click="toggle" class="cursor-pointer hover:text-blue-800">Registrera konto</button>
    </div>
    <div class="text-center mb-4 mt-4 text-blue-600">
      <a href="https://www.markuswebb.se/theproject/web/user/password" class="text-blue-600"
        >Återställ lösenord</a
      >
    </div>
    <p class="text-center mb-4 mt-4" v-if="errorMessage">{{ errorMessage }}</p>
  </div>
</template>

<script lang="ts">
import BlueButton from '@/components/BlueButton.vue'
import { login } from '@/apiService/LoginService'
import { register } from '@/apiService/LoginService'
import SlateButton from '@/components/SlateButton.vue'

export default {
  name: 'LoginView',
  components: {
    BlueButton,
    SlateButton,
  },
  //Dataarray för discar samt variabel för meddelande.
  data() {
    return {
      username: '',
      password: '',
      email: '',
      formTitle: 'Logga in',
      errorMessage: '',
      loginToggle: true,
    }
  },

  methods: {
    //metoden skickar anrop för att registrera eller logga in beroende på om loginToggle är true eller false
    async submit(e: Event) {
      if (this.loginToggle) {
        this.errorMessage = await login(this.username, this.password)
      } else {
        this.errorMessage = await register(this.username, this.email)
      }
    },

    //Används för att ändra läga om användaren ska logga in eller registrera
    toggle() {
      if (this.loginToggle === true) {
        this.loginToggle = false
        this.formTitle = 'Registrera'
      } else {
        this.loginToggle = true
        this.formTitle = 'Logga in'
      }
    },
  },
}
</script>
