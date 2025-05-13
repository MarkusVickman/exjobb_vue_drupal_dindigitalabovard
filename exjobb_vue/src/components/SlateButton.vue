<template>
  <button
    class="bg-slate-700 hover:bg-slate-900 text-white font-bold rounded-full py-2 px-4 hover:cursor-pointer"
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
  name: 'SlateButton',
  props: ['buttonName', 'id'],
  emits: ['clicked'],
}
</script>
