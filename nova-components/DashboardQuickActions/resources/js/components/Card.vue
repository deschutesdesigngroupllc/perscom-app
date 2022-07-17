<template>
  <div class="flex justify-center items-center">
    <div class="w-full">
      <Heading>PERSCOM Personnel Management System</Heading>
      <p class="text-90 leading-tight">
        Providing cutting-edge personnel management for para-military organizations
      </p>

      <Card class="mt-8">
        <div class="md:grid md:grid-cols-2">
          <div class="border-r border-b border-gray-200 dark:border-gray-700">
            <a :href="this.routes?.user" class="no-underline flex p-6">
              <div class="flex justify-center items-center w-10 flex-shrink-0 mr-6">
                <svg class="text-primary-500 dark:text-primary-600" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="none" viewBox="0 0 22 22" stroke="currentColor" stroke-width="1.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
              </div>

              <div>
                <Heading :level="3">Create A New Personnel Profile</Heading>
                <p class="text-90 leading-normal mt-3">
                  Personnel profiles are the foundation of PERSCOM and provide a powerful and robust interface to store data while being displayed in a elegant and useable fashion.
                </p>
              </div>
            </a>
          </div>

          <div class="border-b border-gray-200 dark:border-gray-700">
            <a :href="this.routes?.assignment" class="no-underline flex p-6">
              <div class="flex justify-center items-center w-10 flex-shrink-0 mr-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" class="text-primary-500 dark:text-primary-600" fill="none" viewBox="0 0 22 22" stroke="currentColor" stroke-width="1.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
              </div>

              <div>
                <Heading :level="3">Register A New Assignment</Heading>
                <p class="text-90 leading-normal mt-3">
                  Assignment records provide a transparent ledger for all personnel transfers that happen within your organization.
                </p>
              </div>
            </a>
          </div>

          <div class="border-r border-b border-gray-200 dark:border-gray-700">
            <a :href="this.routes?.service" class="no-underline flex p-6">
              <div class="flex justify-center items-center w-10 flex-shrink-0 mr-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="text-primary-500 dark:text-primary-600" width="50" height="50" fill="none" viewBox="0 0 22 22" stroke="currentColor" stroke-width="1.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>

              <div>
                <Heading :level="3">Create A New Service Record</Heading>
                <p class="text-90 leading-normal mt-3">
                  Service records provide a written history of every action and event that happens to a user. Records can be manually generated or automated through commmon actions.
                </p>
              </div>
            </a>
          </div>

          <div class="border-b border-gray-200 dark:border-gray-700">
            <a :href="this.routes?.combat" class="no-underline flex p-6">
              <div class="flex justify-center items-center w-10 flex-shrink-0 mr-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="text-primary-500 dark:text-primary-600" width="50" height="50" fill="none" viewBox="0 0 22 22" stroke="currentColor" stroke-width="1.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>

              <div>
                <Heading :level="3">Create A New Combat Record</Heading>
                <p class="text-90 leading-normal mt-3">
                  Similiar to Service Records, Combat Records provide a timeline of all combat encounters by the user.
                </p>
              </div>
            </a>
          </div>

          <div class="border-r md:border-b-0 border-b border-gray-200 dark:border-gray-700">
            <a :href="this.routes?.promotion" class="no-underline flex p-6">
              <div class="flex justify-center items-center w-10 flex-shrink-0 mr-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="text-primary-500 dark:text-primary-600" width="50" height="50" fill="none" viewBox="0 0 22 22" stroke="currentColor" stroke-width="1.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
              </div>

              <div>
                <Heading :level="3">File A New Promotion</Heading>
                <p class="text-90 leading-normal mt-3">
                  Assign a new rank to a user, making sure to keep a thorough and detailed history of all changes in rank for the user.
                </p>
              </div>
            </a>
          </div>

          <div class="md:border-b-0 border-b border-gray-200 dark:border-gray-700">
            <a :href="this.routes?.award" class="no-underline flex p-6">
              <div class="flex justify-center items-center w-10 flex-shrink-0 mr-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="text-primary-500 dark:text-primary-600" width="50" height="50" fill="none" viewBox="0 0 22 22" stroke="currentColor" stroke-width="1.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
              </div>

              <div>
                <Heading :level="3">File A New Award</Heading>
                <p class="text-90 leading-normal mt-3">
                  Add an award to a user's personnel file. Awards can be viewed from within the user's personnel file.
                </p>
              </div>
            </a>
          </div>
        </div>
      </Card>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      routes: null
    }
  },
  props: [
    'card',
  ],
  mounted() {
    Nova.request().get('/nova-vendor/dashboard-quick-actions/routes').then(response => {
      this.routes = response.data;
    });
  }
}
</script>
