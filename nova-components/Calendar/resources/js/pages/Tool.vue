<template>
  <div>
    <Head title="Calendar" />
    <Heading class="mb-6">Calendar</Heading>
    <div id="perscom_widget_wrapper">
      <component
        is="script"
        id="perscom_widget"
        data-widget="calendar"
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
const props = defineProps(['jwt', 'tenant_id', 'widget_url'])
</script>
