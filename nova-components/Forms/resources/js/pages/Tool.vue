<template>
  <div>
    <Head title="Forms" />
    <Heading class="mb-6">Forms</Heading>
    <div id="perscom_widget_wrapper">
      <component
        is="script"
        id="perscom_widget"
        data-widget="forms"
        :data-dark="darkMode"
        :data-apikey="props.jwt"
        :data-perscomid="props.tenant_id"
        :src="props.widget_url"
        type="text/javascript"
      >
      </component>
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps(['jwt', 'tenant_id', 'timezone', 'widget_url'])
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
