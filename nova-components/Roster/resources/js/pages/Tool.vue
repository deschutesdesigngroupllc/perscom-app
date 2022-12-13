<template>
  <div>
    <Head title="Roster" />
    <Heading class="roster-mb-6">Roster</Heading>

    <div class="roster-flex-col roster-space-y-6">
      <Card v-for="unit in roster" :key="unit.id">
        <LoadingView :loading="loading">
          <div class="roster-overflow-hidden roster-overflow-x-auto roster-relative roster-rounded-lg">
            <table class="roster-w-full roster-divide-y roster-divide-gray-100 dark:roster-divide-gray-700 roster-table-auto">
              <thead class="roster-bg-gray-50 dark:roster-bg-gray-800">
              <tr>
                <th class="roster-text-center roster-px-2 roster-whitespace-nowrap roster-uppercase roster-text-gray-500 text-xxs roster-tracking-wide roster-py-2" colspan="7">
                  <span>{{ unit.name }}</span>
                </th>
              </tr>
              </thead>
              <tbody>
                <tr class="group" v-for="user in unit.users" v-if="unit.users.length > 0" :key="user.id" @click="goToProfile(user.url)">
                  <td class="roster-w-1/12 roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <div class="roster-text-right roster-items-center roster-flex roster-justify-end">
                      <img class="" style="max-width: 20px;" :src="user.rank.image_url" v-if="user.rank?.image_url">
                      <span class="roster-whitespace-nowrap" v-else-if="user.rank?.abbreviation">
                        {{ user.rank.abbreviation }}
                      </span>
                    </div>
                  </td>
                  <td class="roster-w-3/12 roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <div class="roster-text-left">
                      <span class="roster-whitespace-nowrap">
                        {{ user.name }}
                      </span>
                    </div>
                  </td>
                  <td class="roster-w-3/12 roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <div class="roster-text-left">
                      <span class="roster-whitespace-nowrap" v-if="user.position?.name">
                        {{ user.position.name }}
                      </span>
                    </div>
                  </td>
                  <td class="roster-w-3/12 roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <div class="roster-text-left">
                      <span class="roster-whitespace-nowrap" v-if="user.specialty?.name">
                        {{ user.specialty.name }}<span v-if="user.specialty?.abbreviation">, {{ user.specialty.abbreviation }}
                        </span>
                      </span>
                    </div>
                  </td>
                  <td class="roster-w-1/12 roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <div class="roster-text-center">
                      <span :class="user.status.color" class="roster-inline-flex roster-items-center roster-whitespace-nowrap min-h-6 roster-px-2 roster-rounded-full roster-uppercase roster-text-xs roster-font-bold" v-if="user.status">
                       {{ user.status.name }}
                      </span>
                    </div>
                  </td>
                  <td class="roster-w-1/12 roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <div class="roster-text-center">
                      <span class="roster-inline-flex roster-items-center roster-whitespace-nowrap min-h-6 roster-px-2 roster-rounded-full roster-uppercase roster-text-xs roster-font-bold roster-bg-green-100 roster-text-green-600 dark:roster-bg-green-500 dark:roster-text-green-900" v-if="user.online">
                        Online
                      </span>
                      <span class="roster-inline-flex roster-items-center roster-whitespace-nowrap min-h-6 roster-px-2 roster-rounded-full roster-uppercase roster-text-xs roster-font-bold roster-bg-sky-100 roster-text-sky-600 dark:roster-bg-sky-500 dark:roster-text-sky-900" v-else>
                        Offline
                      </span>
                    </div>
                  </td>
                </tr>
                <tr v-else>
                  <td class="roster-px-2 roster-py-4 roster-whitespace-nowrap dark:roster-bg-gray-800 roster-text-center" colspan="7">
                    <span class="roster-whitespace-nowrap">
                        There are no users assigned to this unit.
                      </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </LoadingView>
      </Card>
    </div>
  </div>
</template>


<script>
export default {
  data: () => ({
    loading: true,
    roster: []
  }),
  mounted() {
    this.loading = true;
    Nova.request().get('/nova-vendor/roster').then(({ data }) => {
      this.roster = data.units;
      this.loading = false;
    });
  },
  methods: {
    goToProfile(url) {
      Nova.visit(url);
    }
  }
}
</script>
