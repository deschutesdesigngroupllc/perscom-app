import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-message-field', IndexField)
  app.component('detail-message-field', DetailField)
  app.component('form-message-field', FormField)
})
