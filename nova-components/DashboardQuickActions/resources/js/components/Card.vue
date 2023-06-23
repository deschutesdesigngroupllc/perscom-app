<template>
  <div v-if="numberOfAdminRoutes?.value > 0">
    <Card class="dashboard-actions-rounded-lg">
      <div class="md:dashboard-actions-grid md:dashboard-actions-grid-cols-1">
        <div
          v-for="(route, key, index) in routes.value.admin"
          :key="index"
          class="dashboard-actions-flex dashboard-actions-items-center dashboard-actions-border-gray-200 dark:dashboard-actions-border-gray-700"
          :class="{
            'odd:md:dashboard-actions-border-r': numberOfAdminRoutes.value > 1,
            'dashboard-actions-border-b': index < numberOfAdminRoutes.value - 1,
            'md:dashboard-actions-border-b-0': index === numberOfAdminRoutes.value - 2
          }"
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
              <p class="text-90 dashboard-actions-leading-normal dashboard-actions-mt-3">
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
const numberOfAdminRoutes = reactive({})
const numberOfUserRoutes = reactive({})

onMounted(() => {
  Nova.request()
    .get('/nova-vendor/dashboard-quick-actions/routes')
    .then((response) => {
      routes.value = response.data
      numberOfAdminRoutes.value = Object.keys(routes.value.admin ?? {}).length
      numberOfUserRoutes.value = Object.keys(routes.value.user ?? {}).length
    })
})
</script>
