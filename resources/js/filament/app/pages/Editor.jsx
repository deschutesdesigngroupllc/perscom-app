import { CodeEditor } from '@/components/code-editor.jsx'
import { useForm } from '@inertiajs/react'
import { Toaster } from 'sonner'

export default function Editor({ page, defaultHtml, defaultCss, defaultJavascript, previewUrl }) {
  const { post, transform } = useForm({
    files: ''
  })

  const handleOnSave = (files) => {
    transform(() => ({
      files: files
    }))

    post(route('tenant.admin.pages.store', { page: page.id }))
  }

  return (
    <main className='h-screen w-full'>
      <CodeEditor html={page.content} onSave={handleOnSave} defaultHtml={defaultHtml} previewUrl={previewUrl} />
      <Toaster />
    </main>
  )
}
