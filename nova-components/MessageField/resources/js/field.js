import DetailField from './components/DetailField'
import FormField from './components/FormField.vue'

Nova.booting((app, store) => {
  app.component('index-message-field', DetailField)
  app.component('detail-message-field', DetailField)
  app.component('form-message-field', FormField)
})
