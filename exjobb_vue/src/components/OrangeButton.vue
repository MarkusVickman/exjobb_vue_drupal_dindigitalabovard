<template>
  <button
    class="bg-orange-500 hover:bg-orange-700 text-white font-bold rounded-full py-2 px-4 hover:cursor-pointer"
    :title="buttonName"
    :aria-label="buttonName"
    @click="$emit('clicked')"
    :id="id"
  >
    {{ buttonName }}
  </button>
</template>

<script lang="ts">
//Buttonklass med props och emit.
export default {
  name: 'OrangeButton',
  props: ['buttonName', 'id'],
  emits: ['clicked'],
}
</script>
