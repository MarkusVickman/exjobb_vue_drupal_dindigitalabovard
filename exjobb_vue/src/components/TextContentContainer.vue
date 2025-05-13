<!--Generell komponent för att visa text-->
<template>
  <div class="block border-b border-zinc-600">
    <h3 class="lg:text-2xl text-xl underline" v-if="title">{{ title }}</h3>
    <p class="lg:text-xl" v-if="text1">
      <b>{{ text1Header }}</b
      >{{ text1 }}
    </p>
    <p class="lg:text-xl" v-if="text2">
      <b>{{ text2Header }}</b
      >{{ text2 }}
    </p>
    <p class="lg:text-xl" v-if="text3">
      <b>{{ text3Header }}</b
      >{{ text3 }}
    </p>
    <p class="lg:text-xl" v-if="text4">
      <b>{{ text4Header }}</b
      >{{ text4 }}
    </p>
    <p class="lg:text-xl" v-if="text5">
      <b>{{ text5Header }}</b
      >{{ text5 }}
    </p>
    <p class="lg:text-xl" v-if="text6">
      <b>{{ text6Header }}</b
      >{{ text6 }}
    </p>
    <slot></slot>
  </div>
</template>

<script lang="ts">
//Buttonklass med props och emit.
export default {
  name: 'TextContentContainer',
  props: [
    'title',
    'text1',
    'text2',
    'text3',
    'text4',
    'text5',
    'text6',
    'text1Header',
    'text2Header',
    'text3Header',
    'text4Header',
    'text5Header',
    'text6Header',
    'id',
  ],
  emits: [''],
}
</script>
