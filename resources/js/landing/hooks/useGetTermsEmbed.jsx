import { useEffect, useState } from 'react'

const useGetTermsEmbed = ({ policyId, policyName, lang = 'en-us', mode = 'direct' }) => {
  const [loading, setLoading] = useState(true)
  const [containerId] = useState(() => `getterms-embed-${Math.random().toString(36).substr(2, 9)}`)

  useEffect(() => {
    const scriptId = 'getterms-embed-js'

    if (!document.getElementById(scriptId)) {
      const script = document.createElement('script')
      script.id = scriptId
      script.src = 'https://app.getterms.io/dist/js/embed.js'
      script.async = true
      script.onload = () => setLoading(false)
      document.body.appendChild(script)
    } else {
      setLoading(false)
    }
  }, [])

  const embedProps = {
    id: containerId,
    className: 'getterms-document-embed',
    'data-getterms': policyId,
    'data-getterms-document': policyName,
    'data-getterms-lang': lang,
    'data-getterms-mode': mode,
    style: { display: loading ? 'none' : 'block' } // Hide until loaded
  }

  return { loading, embedProps }
}

export default useGetTermsEmbed
