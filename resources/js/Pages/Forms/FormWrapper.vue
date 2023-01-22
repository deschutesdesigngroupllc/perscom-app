<template>
    <div class="container">
        <div class="py-5">
            <div class="text-center">
                <h1 class="">{{ title }}</h1>
                <div v-if="description" class="">{{ description }}</div>
            </div>
            <div v-if="instructions" class="form mt-4 bg-white shadow-sm">
                <h3 class="">Instructions:</h3>
                <div class="" v-html="instructions" />
            </div>
            <div v-show="submitted" class="alert alert-success mt-4" role="alert">
                {{ success }}
            </div>
            <CustomForm @response="handleSubmission" class="mt-4 bg-white shadow-sm" :form="form" />
        </div>
    </div>
</template>

<script>
import CustomForm from "./CustomForm.vue";

export default {
    components: { CustomForm },
    data() {
        return {
            submitted: false,
        };
    },
    props: {
        form: Object,
        title: String,
        description: String,
        instructions: String,
        success: {
            type: String,
            default: "The form has been successfully submitted.",
        },
    },
    methods: {
        handleSubmission: function (response, form) {
            form.reset();
            this.submitted = true;
        },
    },
};
</script>

<style>
.container {
    max-width: 700px;
}

.form {
    border-radius: 0.5rem;
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
