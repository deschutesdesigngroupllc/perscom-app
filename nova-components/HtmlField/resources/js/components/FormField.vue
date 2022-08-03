<template>
  <div v-html="field.html"></div>
</template>

<script>
export default {
  props: ['resourceName', 'resourceId', 'field'],
  mounted() {
    this.field.fill = this.fill
  },
  methods: {
    fill(formData) {
    }
  },
}
</script>
