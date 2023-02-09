<template>
  <div class="mb-8" v-if="shouldShowDocument">
    <div class="flex items-center">
      <h1 class="text-90 text-xl font-normal md:text-2xl">{{ documentName }}</h1>
    </div>
    <div class="mt-3 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
      <div v-html="documentContent" />
    </div>
  </div>
</template>

<script>
export default {
  props: ['resourceName', 'resourceId', 'panel'],
  computed: {
    documentName: function () {
      return this.panel.fields[0].title ?? 'Document'
    },
    documentContent: function () {
      return this.panel.fields[0].content ?? null
    },
    shouldShowDocument: function () {
      return this.documentContent !== null
    }
  }
}
</script>
