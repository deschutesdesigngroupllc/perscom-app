<template>
  <div class="flex justify-center items-center">
    <div class="w-full">
      <Heading>Latest Form Submissions</Heading>
      <p class="text-90 leading-tight">
        A collection of the latest forms submitted to your organization
      </p>

      <Card class="mt-8 flex flex-col h-auto p-2">
        <table class="table w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="p-4 text-left text-90 uppercase tracking-wide font-bold text-xs">Name</th>
              <th scope="col" class="p-4 text-left text-90 uppercase tracking-wide font-bold text-xs">Form</th>
              <th scope="col" class="p-4 text-left text-90 uppercase tracking-wide font-bold text-xs">Date</th>
              <th scope="col" class="relative p-4">
                <span class="sr-only">Edit</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="submission in this.submissions" :key="submission.id">
              <td class="whitespace-nowrap p-4 text-sm font-medium">{{ submission.user }}</td>
              <td class="whitespace-nowrap p-4 text-sm">{{ submission.form }}</td>
              <td class="whitespace-nowrap p-4 text-sm">{{ submission.date }}</td>
              <td class="relative whitespace-nowrap p-4 text-right font-medium">
                <a :href="submission.view_url" class="text-primary-600 hover:text-primary-900">View Submission</a>
              </td>
            </tr>
          </tbody>
        </table>
      </Card>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      submissions: []
    }
  },
  props: [
    'card',
  ],
  mounted() {
    Nova.request().get('/nova-vendor/latest-form-submissions/all').then(response => {
      this.submissions = response.data;
    });
  }
}
</script>
