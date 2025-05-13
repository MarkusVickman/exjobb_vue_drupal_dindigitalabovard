<script setup lang="ts">
import { RouterView } from 'vue-router'
import Footer from './components/Footer.vue'
import Header from './components/Header.vue'
</script>

<template>
  <Header />
  <main class="flex-grow mt-24">
    <RouterView />
  </main>
  <Footer />
</template>
