const featureOsWidget = require('./widget')

Nova.request()
  .get('/nova-vendor/featureos/login')
  .then((response) => {
    const widget = new featureOsWidget({
      modules: ['feature_requests'],
      type: 'modal',
      openFrom: 'right',
      theme: 'light',
      accent: '#2563eb',
      selector: 'no',
      jwtToken: response.data.jwt ?? null,
      token: 'hOeHRslcz67LehEwbFdJyQ',
      submissionBucketIds: [14131, 14130],
      showOnlySubmission: true
    })
    widget.init()
  })
