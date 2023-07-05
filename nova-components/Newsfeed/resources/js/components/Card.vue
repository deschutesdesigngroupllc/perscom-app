<template>
  <div>
    <Card class="px-6 py-4 mb-6">
      <Heading class="font-semibold">PERSCOM Personnel Management System</Heading>
      <p class="text-90 leading-tight text-gray-400">Personnel management made easy for high-performing, results-driven organizations.</p>
    </Card>
    <div id="perscom_widget_wrapper">
      <component
        is="script"
        id="perscom_widget"
        data-widget="newsfeed"
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
const props = defineProps(['card'])
</script>
