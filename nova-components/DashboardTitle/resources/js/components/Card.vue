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
import {onMounted} from "vue";

const props = defineProps(['card'])

const widgetScript = document.createElement('script')
widgetScript.setAttribute('src', 'https://widgets-v3.featureos.app/widget.js')
document.head.appendChild(widgetScript)

onMounted(() => {
  console.log(window)
  const widget = new window.HellonextWidget({
    "token": "hOeHRslcz67LehEwbFdJyQ",
    "jwtToken": "JWT TOKEN CREATED WITH SSO KEY", // Optional: This token allows the widget to uniquely identify your user. Without the token, the user will have to enter his email to submit a post.
    "modules": [
      "feature_requests"
    ],
    "type": "popover",
    "openFrom": "right",
    "theme": "light",
    "selector": "#widgetID",
  })
  widget.init()
})
</script>

