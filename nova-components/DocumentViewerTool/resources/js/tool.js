import Tool from './components/Tool'

Nova.booting((app, store) => {
  app.component('document-viewer-tool', Tool)
})
