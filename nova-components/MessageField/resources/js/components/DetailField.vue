<template>
  <div
    style="margin-left: -1.5rem; margin-right: -1.5rem; margin-top: -0.5rem"
    v-bind:class="{
      'mf-bg-red-500 mf-text-red-100': field.color === 'danger',
      'mf-bg-green-500 mf-text-green-100': field.color === 'success',
      'mf-bg-sky-500 mf-text-sky-100': field.color === 'info',
      'mf-bg-amber-500 mf-text-amber-100': field.color === 'warning'
    }"
  >
    <div class="mf-px-6 mf-py-2">
      <div class="mf-font-extrabold" v-text="field.text" />
    </div>
  </div>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field']
}
</script>
