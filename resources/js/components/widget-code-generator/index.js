export default function widgetCodeGenerator({ state }) {
  return {
    state,
    copyCode: function () {
      return new Promise((resolve, reject) => {
        try {
          window.navigator.clipboard.writeText(this.state)
          resolve()
        } catch (error) {
          reject(error)
        }
      })
    },
    updateCode: function (state) {
      this.state = state
    }
  }
}
