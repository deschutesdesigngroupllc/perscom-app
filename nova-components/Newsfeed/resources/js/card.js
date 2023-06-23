import Card from './components/Card'

Nova.booting((app, store) => {
  app.component('newsfeed', Card)
})
