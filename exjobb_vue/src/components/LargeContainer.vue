<!--Generell komponent för att visa text-->
<template>
  <article class="lg:ml-6 lg:mr-6 mr-2 ml-2 lg:w-4xl mt-12 bg-zinc-100 p-12 shadow-sm rounded-3xl">
    <h2 class="text-2xl underline mb-6" v-if="title">{{ title }}</h2>
    <p class="text-xl" v-if="text1">{{ text1 }}</p>
    <slot></slot>
  </article>
</template>

<script lang="ts">
//Buttonklass med props och emit.
export default {
  name: 'LargeContainer',
  props: ['title', 'text1', 'id'],
  emits: [''],
}
</script>
