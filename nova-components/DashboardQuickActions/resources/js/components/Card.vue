<template>
  <div v-if="routes.value && Object.keys(routes.value).length > 0">
    <Card class="da-rounded-lg">
      <div class="md:da-grid md:da-grid-cols-1">
        <div
          v-for="(route, key, index) in routes.value"
          :key="index"
          :class="{ 'da-border-none': index === Object.keys(routes.value).length - 1 }"
          class="da-flex da-border-b da-items-center da-border-gray-200 dark:da-border-gray-700 hover:da-bg-gray-100 dark:hover:da-bg-gray-700"
        >
          <a :href="route.link" class="da-no-underline da-flex da-p-6">
            <div
              class="da-flex da-justify-center da-items-center da-w-10 da-flex-shrink-0 da-mr-6"
            >
              <svg
                class="text-primary-500 dark:text-primary-600"
                xmlns="http://www.w3.org/2000/svg"
                width="50"
                height="50"
                fill="none"
                viewBox="0 0 22 22"
                stroke="currentColor"
                stroke-width="1.2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" :d="route.icon" />
              </svg>
            </div>

            <div>
              <Heading :level="3" class="dark:da-text-gray-400">{{ route.title }}</Heading>
              <p class="da-leading-normal da-mt-3 dark:da-text-gray-400">
                {{ route.description }}
              </p>
            </div>
          </a>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup>
import {onMounted, reactive} from 'vue'

const routes = reactive({})

onMounted(() => {
  Nova.request()
    .get('/nova-vendor/dashboard-quick-actions/routes')
    .then((response) => {
      routes.value = response.data
    })
})
</script>
