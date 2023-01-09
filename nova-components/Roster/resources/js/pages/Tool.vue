<template>
  <div>
    <Head title="Roster" />
    <Heading class="roster-mb-6">Roster</Heading>

    <div class="roster-flex-col roster-space-y-6">
      <Card v-for="unit in roster.value" :key="unit.id">
        <LoadingView :loading="loading.value">
            <div class="roster-overflow-hidden roster-shadow roster-rounded-md">
                <div class="dark:roster-bg-gray-700 roster-bg-gray-50 roster-p-4 roster-border-b dark:roster-border-gray-700 roster-flex roster-justify-center roster-font-bold">
                    {{ unit.name }}
                </div>
                <ul role="list" class="roster-divide-y roster-divide-gray-200 dark:roster-divide-gray-700">
                    <li v-for="user in unit.users" v-if="unit.users.length > 0" :key="user.id" @click="goToProfile(user.url)">
                        <a href="#" class="roster-block hover:roster-bg-gray-50 dark:hover:roster-bg-gray-700">
                            <div class="roster-px-4 roster-py-4 sm:roster-px-6 roster-flex roster-justify-start roster-items-center roster-space-x-4">
                                <div class="roster-flex-grow-0">
                                    <img class="roster-h-10" :src="user.rank.image_url" v-if="user.rank?.image_url">
                                    <span class="roster-whitespace-nowrap roster-font-bold" v-else-if="user.rank?.abbreviation">
                                        {{ user.rank.abbreviation }}
                                    </span>
                                </div>
                                <div class="roster-flex-auto">
                                    <div class="roster-flex roster-items-center roster-justify-between">
                                        <p class="roster-truncate roster-text-sm roster-font-semibold">{{ user.name }}</p>
                                        <div class="roster-ml-2 roster-flex roster-flex-shrink-0">
                                            <p :class="user.status.color" class="roster-inline-flex roster-rounded-full roster-px-2 roster-text-xs roster-font-bold roster-leading-5 roster-uppercase">{{ user.status.name }}</p>
                                        </div>
                                    </div>
                                    <div class="roster-mt-2 sm:roster-flex sm:roster-justify-between">
                                        <div class="sm:roster-flex">
                                            <p v-if="user.position?.name" class="roster-flex roster-items-center roster-text-sm roster-text-gray-500">
                                                <svg class="roster-mr-1.5 roster-h-5 roster-w-5 roster-flex-shrink-0 roster-text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.305 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z" />
                                                </svg>
                                                {{ user.position.name }}
                                            </p>
                                            <p v-if="user.specialty?.name" class="roster-mt-2 roster-flex roster-items-center roster-text-sm roster-text-gray-500 sm:roster-mt-0 sm:roster-ml-6">
                                                <svg class="roster-mr-1.5 roster-h-5 roster-w-5 roster-flex-shrink-0 roster-text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M6 3.75A2.75 2.75 0 018.75 1h2.5A2.75 2.75 0 0114 3.75v.443c.572.055 1.14.122 1.706.2C17.053 4.582 18 5.75 18 7.07v3.469c0 1.126-.694 2.191-1.83 2.54-1.952.599-4.024.921-6.17.921s-4.219-.322-6.17-.921C2.694 12.73 2 11.665 2 10.539V7.07c0-1.321.947-2.489 2.294-2.676A41.047 41.047 0 016 4.193V3.75zm6.5 0v.325a41.622 41.622 0 00-5 0V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25zM10 10a1 1 0 00-1 1v.01a1 1 0 001 1h.01a1 1 0 001-1V11a1 1 0 00-1-1H10z" clip-rule="evenodd" />
                                                    <path d="M3 15.055v-.684c.126.053.255.1.39.142 2.092.642 4.313.987 6.61.987 2.297 0 4.518-.345 6.61-.987.135-.041.264-.089.39-.142v.684c0 1.347-.985 2.53-2.363 2.686a41.454 41.454 0 01-9.274 0C3.985 17.585 3 16.402 3 15.055z" />
                                                </svg>
                                                {{ user.specialty.name }}<span v-if="user.specialty?.abbreviation">, {{ user.specialty.abbreviation }}</span>
                                            </p>
                                        </div>
                                        <div class="roster-mt-2 roster-flex roster-items-center roster-text-sm roster-text-gray-500 sm:roster-mt-0">
                                            <svg class="roster-mr-1.5 roster-h-5 roster-w-5 roster-flex-shrink-0 roster-text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                                <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                            </svg>
                                            <a :href="'mailto:' + user.email">{{ user.email }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </LoadingView>
      </Card>
    </div>
  </div>
</template>

<script setup>
import {onMounted, reactive} from 'vue';

const loading = reactive({});
const roster = reactive({});

onMounted(() => {
    loading.value = true;
    Nova.request().get('/nova-vendor/roster').then(({ data }) => {
        roster.value = data;
        loading.value = false;
    });
})

function goToProfile(url) {
    Nova.visit(url)
}
</script>
