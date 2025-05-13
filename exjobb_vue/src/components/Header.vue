<template>
  <!--header med olika länkar och mobilmeny -->
  <header class="space-x-12 left-0 w-full flex">
    <nav aria-label="Huvudmeny">
      <RouterLink to="/" class="z-2">
        <img
          alt="House logo"
          class="logo hover:shadow-md hover:rounded-xl absolute z-2"
          src="@/assets/logo.svg"
          width="75"
          height="75"
          title="Startsidan"
      /></RouterLink>
      <div
        v-if="isLoggedIn"
        id="nav"
        class="xl:space-x-12 z-1 xl:text-center xl:w-full xl:p-5 xl:text-xl fixed xl:absolute w-full h-full hidden xl:block xl:h-fit bg-gradient-to-tr from-violet-100 to-zinc-50 xl:bg-none"
      >
        <RouterLink
          @click="closePhoneMenu"
          class="hover:font-semibold p-15 xl:w-fit w-full text-center block xl:inline hover:shadow-md hover:rounded-xl xl:hover:shadow-none underline xl:no-underline"
          to="/Home"
          >Fastigheter</RouterLink
        >
        <RouterLink
          @click="closePhoneMenu"
          class="hover:font-semibold p-15 xl:w-fit w-full text-center block xl:inline hover:shadow-md hover:rounded-xl xl:hover:shadow-none underline xl:no-underline"
          to="/invoice"
          >Fakturor</RouterLink
        >
        <RouterLink
          @click="closePhoneMenu"
          class="hover:font-semibold p-15 xl:w-fit w-full text-center block xl:inline hover:shadow-md hover:rounded-xl xl:hover:shadow-none underline xl:no-underline"
          to="/reports"
          >Felanmälningar</RouterLink
        >
        <RouterLink
          @click="closePhoneMenu"
          class="hover:font-semibold p-15 xl:w-fit w-full text-center block xl:inline hover:shadow-md hover:rounded-xl xl:hover:shadow-none underline xl:no-underline"
          to="/information"
          >Informera</RouterLink
        >
        <OrangeButton
          buttonName="Logga ut"
          @clicked="logoutClicked"
          class="hover:font-semibold block m-auto mt-6 xl:absolute xl:right-10 xl:top-5 xl:mt-0 xl:text-base"
          id="logoutButton"
        />
        <button class="xl:hidden absolute top-2.5 right-0" @click="closePhoneMenu">
          <img
            alt="menu close logo"
            class="logo hover:shadow-md hover:rounded-sm p-2"
            src="@/assets/cross.svg"
            width="55"
            height="55"
            title="Menu close"
          />
        </button>
      </div>
      <button v-if="isLoggedIn" class="xl:hidden absolute top-2.5 right-0" @click="openPhoneMenu">
        <img
          alt="menu logo"
          class="logo hover:shadow-md hover:rounded-sm p-2"
          src="@/assets/menu-burger.svg"
          width="55"
          height="55"
          title="Menu"
        />
      </button>
      <BlueButton
        buttonName="Logga in"
        @clicked="loginClicked"
        class="absolute right-10 top-5 xl:z-1"
        id="loginButton"
        v-if="!isLoggedIn"
      />
    </nav>
  </header>
</template>

<script lang="ts">
import router from '@/router'
import BlueButton from './BlueButton.vue'
import OrangeButton from './OrangeButton.vue'
import { logout } from '@/apiService/LoginService'

export default {
  name: 'HomeView',
  components: {
    BlueButton,
    OrangeButton,
  },
  //Data
  data() {
    return {
      isLoggedIn: false,
    }
  },
  mounted() {
    this.checkLoginStatus()
  },

  //Watcher för vad som ska göras om inloggningsstatus ändras
  watch: {
    isLoggedIn: {
      handler(newValue) {
        this.isLoggedIn = newValue
      },
      deep: true,
      immediate: true,
    },
    // Denna watcher kommer att aktiveras när rutten ändras
    $route(to, from) {
      this.checkLoginStatus()
    },
  },

  //Metoder nedan är för inloggning ska ändras
  methods: {
    //Metod som kontrollerar inloggning / om det finns token och dirigeras samt fixar boolean till rätt
    checkLoginStatus() {
      if (!localStorage.getItem('access_token')) {
        this.isLoggedIn = false
      } else {
        this.isLoggedIn = true
      }
    },

    loginClicked() {
      router.push('/login')
    },

    logoutClicked() {
      localStorage.removeItem('access_token')
      router.push('/login')
      this.isLoggedIn = false
    },

    //Stänger mobilmeny
    closePhoneMenu() {
      if (window.innerWidth <= 1280) {
        ;(document.getElementById('nav') as HTMLElement).style.display = 'none'
      }
    },

    //öppnar mobilmeny
    openPhoneMenu() {
      ;(document.getElementById('nav') as HTMLElement).style.display = 'block'
    },
  },
}
</script>
