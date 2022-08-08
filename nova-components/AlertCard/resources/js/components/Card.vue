<template>
    <div v-if="this.card.announcements.length" class="ac-flex ac-flex-col ac-space-y-5">
        <div
            v-for="announcement in this.card.announcements"
            class="ac-rounded-md ac-p-4"
            :class="{
                'ac-bg-yellow-50': announcement.color === 'warning',
                'ac-bg-blue-100': announcement.color === 'info',
                'ac-bg-green-100': announcement.color === 'success',
                'ac-bg-red-100': announcement.color === 'danger',
            }"
        >
            <div class="ac-flex">
                <div class="ac-flex-shrink-0">
                    <svg
                        class="ac-h-5 ac-w-5"
                        :class="{
                            'ac-text-yellow-400': announcement.color === 'warning',
                            'ac-text-blue-400': announcement.color === 'info',
                            'ac-text-green-400': announcement.color === 'success',
                            'ac-text-red-400': announcement.color === 'danger',
                        }"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            v-if="announcement.color === 'warning'"
                            fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                        />
                        <path
                            v-if="announcement.color === 'danger'"
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                        <path
                            v-if="announcement.color === 'success'"
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                        />
                        <path
                            v-if="announcement.color === 'info'"
                            fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ac-ml-3">
                    <h3
                        class="ac-text-sm ac-font-bold"
                        :class="{
                            'ac-text-yellow-800': announcement.color === 'warning',
                            'ac-text-blue-800': announcement.color === 'info',
                            'ac-text-green-800': announcement.color === 'success',
                            'ac-text-red-800': announcement.color === 'danger',
                        }"
                    >
                        {{ announcement.title }}
                    </h3>
                    <div
                        class="ac-mt-2 ac-text-sm"
                        :class="{
                            'ac-text-yellow-700': announcement.color === 'warning',
                            'ac-text-blue-700': announcement.color === 'info',
                            'ac-text-green-700': announcement.color === 'success',
                            'ac-text-red-700': announcement.color === 'danger',
                        }"
                    >
                        <p v-html="announcement.content"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["card"],
    mounted() {
      console.log(this.card.announcements)
    }
};
</script>
