<template>
  <div>
    <Card class="mb-6 px-6 py-4">
      <Heading class="font-semibold">PERSCOM Personnel Management System</Heading>
      <p class="text-90 leading-tight text-gray-400">Personnel management made easy for high-performing, results-driven organizations.</p>
    </Card>
    <div id="perscom_widget_wrapper">
      <component
        is="script"
        id="perscom_widget"
        data-widget="newsfeed"
        :data-dark="darkMode"
        :data-apikey="props.card.jwt"
        :data-perscomid="props.card.tenant_id"
        :src="props.card.widget_url"
        data-limit="4"
        type="text/javascript"
      >
      </component>
    </div>
  </div>
</template>

<script setup>
import {onBeforeUnmount, onMounted, ref} from 'vue'

const props = defineProps(['card'])
const darkMode = ref(document.documentElement.classList.contains('dark'))
const observer = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.attributeName === 'class') {
      darkMode.value = document.documentElement.classList.contains('dark')

      const iframe = document.getElementById('perscom_widget_iframe')

      if (iframe) {
        iframe.iFrameResizer.sendMessage({
          darkMode: darkMode.value
        })
      }
    }
  })
})

onMounted(() => {
  observer.observe(document.documentElement, {
    attributes: true
  })
})

onBeforeUnmount(() => {
  observer.disconnect()
})
</script>
