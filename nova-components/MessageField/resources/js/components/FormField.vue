<template>
  <div
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
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field']
}
</script>
