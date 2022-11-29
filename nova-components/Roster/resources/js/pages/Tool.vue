<template>
  <div>
    <Head title="Roster" />
    <Heading class="roster-mb-6">Roster</Heading>

    <div class="roster-flex-col roster-space-y-6">
      <Card v-for="unit in units" :key="unit.id">
        <LoadingView :loading="loading">
          <div class="roster-overflow-hidden roster-overflow-x-auto roster-relative roster-rounded-lg">
            <table class="roster-w-full roster-divide-y roster-divide-gray-100 dark:roster-divide-gray-700 roster-table-auto">
              <thead class="roster-bg-gray-50 dark:roster-bg-gray-800">
              <tr>
                <th class="roster-text-center roster-px-2 roster-whitespace-nowrap roster-uppercase roster-text-gray-500 text-xxs roster-tracking-wide roster-py-2" colspan="6">
                  <span>{{ unit.name }}</span>
                </th>
              </tr>
              </thead>
              <tbody class="roster-divide-y roster-divide-gray-100 dark:roster-divide-gray-700">
              <tr class="group">
                <td class="roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:roster-bg-gray-50 dark:group-hover:roster-bg-gray-900" >
                  <div class="roster-text-center">
                    <span class="roster-whitespace-nowrap">
                      Captain
                    </span>
                  </div>
                </td>
                <td class="roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:roster-bg-gray-50 dark:group-hover:roster-bg-gray-900">
                  <div class="roster-text-left">
                    <span class="roster-whitespace-nowrap">
                      Jon Erickson
                    </span>
                  </div>
                </td>
                <td class="roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:roster-bg-gray-50 dark:group-hover:roster-bg-gray-900">
                  <div class="roster-text-left">
                    <span class="roster-whitespace-nowrap">
                      Company Commander, Headquarters Company
                    </span>
                  </div>
                </td>
                <td class="roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:roster-bg-gray-50 dark:group-hover:roster-bg-gray-900">
                  <div class="roster-text-left">
                    <span class="roster-whitespace-nowrap">
                      Infantry Officer
                    </span>
                  </div>
                </td>
                <td class="roster-px-2 roster-py-2 roster-whitespace-nowrap roster-cursor-pointer dark:roster-bg-gray-800 group-hover:roster-bg-gray-50 dark:group-hover:roster-bg-gray-900">
                  <div class="roster-text-left">
                    <span class="roster-whitespace-nowrap">
                      Active
                    </span>
                  </div>
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
    units: []
  }),
  mounted() {
    this.loading = true;
    Nova.request().get('/nova-vendor/roster').then(({ data }) => {
      this.units = data.units;
      this.loading = false;
    });
  }
}
</script>
