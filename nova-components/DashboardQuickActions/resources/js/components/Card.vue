<template>
  <div v-if="routes.value && Object.keys(routes.value).length > 0">
    <Card class="dashboard-actions-rounded-lg">
      <div class="md:dashboard-actions-grid md:dashboard-actions-grid-cols-1">
        <div
          v-for="(route, key, index) in routes.value"
          :key="index"
          :class="{'dashboard-actions-border-none': index === Object.keys(routes.value).length - 1}"
          class="dashboard-actions-flex dashboard-actions-border-b dashboard-actions-items-center dashboard-actions-border-gray-200 dark:dashboard-actions-border-gray-700 hover:bg-gray-100"
        >
          <a :href="route.link" class="dashboard-actions-no-underline dashboard-actions-flex dashboard-actions-p-6">
            <div
              class="dashboard-actions-flex dashboard-actions-justify-center dashboard-actions-items-center dashboard-actions-w-10 dashboard-actions-flex-shrink-0 dashboard-actions-mr-6"
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
              <Heading :level="3">{{ route.title }}</Heading>
              <p class="dashboard-actions-leading-normal dashboard-actions-mt-3">
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
import { onMounted, reactive } from 'vue'

const routes = reactive({})

onMounted(() => {
  Nova.request()
    .get('/nova-vendor/dashboard-quick-actions/routes')
    .then((response) => {
      routes.value = response.data
    })
})
</script>
