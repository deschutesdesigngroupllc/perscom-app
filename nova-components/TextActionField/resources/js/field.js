import IndexField from '@/fields/Index/TextField'
import DetailField from '@/fields/Detail/TextField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-text-action-field', IndexField)
  app.component('detail-text-action-field', DetailField)
  app.component('form-text-action-field', FormField)
})
