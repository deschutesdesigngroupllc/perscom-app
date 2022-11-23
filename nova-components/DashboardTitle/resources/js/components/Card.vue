<template>
  <div class="flex justify-center items-center" v-if="this.card.title || this.card.subtitle">
    <div class="w-full">
      <Heading class="font-bold" style="font-weight: 600;" v-if="this.card.title">{{ this.card.title }}</Heading>
      <p class="text-90 leading-tight" v-if="this.card.subtitle">
        {{ this.card.subtitle }}
      </p>
    </div>
  </div>
</template>

<script>
export default {
  props: ["card"]
}
</script>
