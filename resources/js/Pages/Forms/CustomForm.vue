<template>
    <div class="container">
        <div class="py-5">
            <div class="text-center">
                <h1 class="tw-text-gray-600">{{ title }}</h1>
                <div v-if="description" class="tw-text-gray-600">{{ description }}</div>
            </div>
            <div v-if="instructions" class="form mt-4 bg-white shadow-sm">
                <h3 class="tw-text-gray-700">Instructions:</h3>
                <div class="tw-text-gray-600" v-html="instructions" />
            </div>
            <laraform :form="form" class="mt-4 bg-white shadow-sm"></laraform>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        form: Object,
        title: String,
        description: String,
        instructions: String,
    },
};
</script>

<style>
.form {
    border-radius: 0.5rem;
    max-width: 700px;
    padding: 30px;
    margin: auto;
}
h1,
h2,
h3,
h4,
h5 {
    line-height: 1.25;
}
h1 {
    font-size: 1.5rem;
    font-weight: 400;
}
h3 {
    font-size: 0.875rem;
    font-weight: 700;
}
.form-button {
    color: white;
    font-weight: 600;
    font-size: 0.875rem !important;
    line-height: 1.5rem !important;
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
    padding-left: 1rem;
    padding-right: 1rem;
    border-radius: 9999px !important;
}
</style>
