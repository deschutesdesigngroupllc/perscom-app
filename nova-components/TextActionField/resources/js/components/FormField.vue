<template>
  <DefaultField :field="currentField" :errors="errors" :show-help-text="showHelpText" :full-width-content="fullWidthContent">
    <template #field>
      <div class="space-y-4">
        <div class="flex items-center space-x-2">
          <input
            v-bind="extraAttributes"
            class="form-control form-input-bordered flex-grow-1 form-input"
            :style="{ 'flex-grow': '1' }"
            @input="handleChange"
            :value="value"
            :id="currentField.uniqueKey"
            :dusk="field.attribute"
            :disabled="currentlyIsReadonly"
          />

          <BasicButton
            @click="handleAction"
            type="button"
            class="bg-primary-500 hover:bg-primary-400 relative overflow-hidden whitespace-nowrap text-white shadow dark:text-gray-900"
            >{{ field.actionText }}</BasicButton
          >
        </div>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import {FormField, HandlesValidationErrors} from 'laravel-nova'

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ['resourceName', 'resourceId', 'field'],
  methods: {
    setInitialValue() {
      this.value = this.field.value || ''
    },

    fill(formData) {
      formData.append(this.fieldAttribute, this.value || '')
    },

    handleAction() {
      Nova.request()
        .post('/nova-vendor/text-field-action/action', {
          actionCallback: this.field.actionCallback ?? null
        })
        .then((response) => {
          if (response.data.value) {
            this.value = response.data.value
          }

          Nova.success(this.field.actionMessage || 'The action was successful.')
        })
    }
  }
}
</script>
