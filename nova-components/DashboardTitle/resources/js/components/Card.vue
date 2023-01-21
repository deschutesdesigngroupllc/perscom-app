<template>
    <div
        class="dashboard-title-flex dashboard-title-justify-center dashboard-title-items-center"
        v-if="props.card.title || props.card.subtitle"
    >
        <div class="dashboard-title-w-full">
            <Heading class="dashboard-title-font-bold" v-if="props.card.title">{{ props.card.title }}</Heading>
            <p class="text-90 dashboard-title-leading-tight" v-if="props.card.subtitle">
                {{ props.card.subtitle }}
            </p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps(["card"]);
</script>
