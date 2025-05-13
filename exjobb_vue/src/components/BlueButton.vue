<template>
  <button
    class="bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full py-2 px-4 hover:cursor-pointer"
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
  name: 'BlueButton',
  props: ['buttonName', 'id'],
  emits: ['clicked'],
}
</script>
