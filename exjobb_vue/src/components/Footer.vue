<script setup lang="ts"></script>

<template>
  <footer class="w-full">
    <nav
      aria-label="Navigering till information"
      class="space-x-12 space-y-1 p-5 left-0 w-full text-center text-blue-800 text-base"
    >
      <RouterLink class="hover:font-semibold lg:inline block w-full" to="/about"
        >Om tjänsten</RouterLink
      >
      <RouterLink class="hover:font-semibold lg:inline block w-full" to="/privacypolicy"
        >Integritetspolicy</RouterLink
      >
      <RouterLink class="hover:font-semibold lg:inline block w-full" to="/accessibility"
        >Tillgänglighetsredogörelse</RouterLink
      >
    </nav>
  </footer>
</template>
