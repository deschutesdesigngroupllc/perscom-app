<template>
  <div
    class="dt-flex dt-justify-center dt-items-center"
    v-if="props.card.title || props.card.subtitle"
  >
    <div class="dt-w-full">
      <Heading class="dt-font-bold" v-if="props.card.title">{{ props.card.title }}</Heading>
      <p class="dt-text-opacity-90 dt-leading-tight" v-if="props.card.subtitle">
        {{ props.card.subtitle }}
      </p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps(['card'])
</script>
