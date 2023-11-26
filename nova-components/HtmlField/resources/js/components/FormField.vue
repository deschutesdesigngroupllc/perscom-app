<template>
  <div v-html="field.html"></div>
</template>

<script>
export default {
  props: ['resourceName', 'resourceId', 'field']
}
</script>
