<template>
  <div>
    <Head title="Forms" />
    <Heading class="mb-6">Forms</Heading>
    <div id="perscom_widget_wrapper">
      <component
        is="script"
        id="perscom_widget"
        data-widget="forms"
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
const props = defineProps(['jwt', 'tenant_id', 'timezone', 'widget_url'])
</script>
