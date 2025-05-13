<template>
  <button
    class="hover:cursor-pointer"
    @click="$emit('clicked')"
    :id="id"
    title="Stäng"
    aria-label="Stäng rutan"
  >
    <img
      alt="menu close logo"
      class="logo hover:shadow-md hover:rounded-sm p-2"
      src="@/assets/cross.svg"
      width="55"
      height="55"
    />
  </button>
</template>

<script lang="ts">
//Buttonklass med props och emit.
export default {
  name: 'CrossButton',
  props: ['id'],
  emits: ['clicked'],
}
</script>
