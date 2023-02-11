<template>
  <div>
    <Head title="Roster" />
    <Heading class="mb-6">Roster</Heading>
    <div id="perscom_widget_wrapper">
      <component
        is="script"
        id="perscom_widget"
        data-widget="roster"
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
