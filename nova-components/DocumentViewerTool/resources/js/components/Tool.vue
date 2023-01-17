<template>
    <div class="mb-8" v-if="shouldShowDocument">
        <div class="flex items-center">
            <h1 class="text-90 font-normal text-xl md:text-2xl">{{ documentName }}</h1>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-3 p-6">
            <div v-html="documentContent"/>
        </div>
    </div>
</template>

<script>
export default {
    props: ['resourceName', 'resourceId', 'panel'],
    computed: {
        documentName: function () {
            return this.panel.fields[0].title ?? 'Document'
        },
        documentContent: function () {
            return this.panel.fields[0].content ?? null
        },
        shouldShowDocument: function () {
            return this.documentContent !== null
        }
    }
}
</script>
