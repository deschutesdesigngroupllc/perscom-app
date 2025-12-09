import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu'
import { Kbd } from '@/components/ui/kbd'
import { Separator } from '@/components/ui/separator'
import { cn } from '@/lib/utils'
import { usePage } from '@inertiajs/react'
import { Editor } from '@monaco-editor/react'
import { Code2, Copy, Download, Eye, EyeOff, FileCode, Maximize2, Minimize2, Moon, Sun } from 'lucide-react'
import { useEffect, useRef, useState } from 'react'
import { toast } from 'sonner'

const FONT_SIZES = [10, 12, 14, 16, 18, 20, 24];

export function CodeEditor({ html, onSave, defaultHtml }) {
  const { name } = usePage().props
  const previewRef = useRef(null)
  const editorRef = useRef(null)
  const [activeFileId, setActiveFileId] = useState(1)
  const [theme, setTheme] = useState('dark');
  const [fontSize, setFontSize] = useState(14)
  const [lineNumbers, setLineNumbers] = useState(true)
  const [minimap, setMinimap] = useState(false)
  const [wordWrap, setWordWrap] = useState(false)
  const [isFullscreen, setIsFullscreen] = useState(false)
  const [showPreview, setShowPreview] = useState(false)
  const [files, setFiles] = useState(() => [
    {
      id: 1,
      name: 'index.html',
      language: 'html',
      content: html || defaultHtml
    }
  ])
  const [savedFiles, setSavedFiles] = useState(() => [
    {
      id: 1,
      name: 'index.html',
      language: 'html',
      content: html || defaultHtml
    }
  ])

  const activeFile = files.find((f) => f.id === activeFileId) || files[0]

  const hasUnsavedChanges = files.some((file, index) => file.content !== savedFiles[index]?.content)

  const canPreview = ['html'].includes(activeFile.language)

  useEffect(() => {
    const root = document.documentElement
    if (theme === 'dark') {
      root.classList.add('dark')
    } else {
      root.classList.remove('dark')
    }
  }, [theme])

  useEffect(() => {
    if (showPreview && previewRef.current && canPreview) {
      updatePreview()
    }
  }, [activeFile.content, showPreview, canPreview, files])

  useEffect(() => {
    const handleBeforeUnload = (e) => {
      if (hasUnsavedChanges) {
        e.preventDefault()
        e.returnValue = ''
      }
    }

    window.addEventListener('beforeunload', handleBeforeUnload)

    return () => {
      window.removeEventListener('beforeunload', handleBeforeUnload)
    }
  }, [hasUnsavedChanges])

  const updatePreview = () => {
    if (!previewRef.current) return

    const iframe = previewRef.current
    const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document

    if (!iframeDoc) return

    const htmlFile = files.find((f) => f.name === 'index.html')

    const bodyContent = htmlFile?.content || ''

    const tailwindAndStyles = `
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                :root {
                    --background: oklch(1 0 0);
                    --foreground: oklch(0.145 0 0);
                    --card: oklch(1 0 0);
                    --card-foreground: oklch(0.145 0 0);
                    --popover: oklch(1 0 0);
                    --popover-foreground: oklch(0.145 0 0);
                    --primary: oklch(0.205 0 0);
                    --primary-foreground: oklch(0.985 0 0);
                    --secondary: oklch(0.97 0 0);
                    --secondary-foreground: oklch(0.205 0 0);
                    --muted: oklch(0.97 0 0);
                    --muted-foreground: oklch(0.556 0 0);
                    --accent: oklch(0.97 0 0);
                    --accent-foreground: oklch(0.205 0 0);
                    --destructive: oklch(0.577 0.245 27.325);
                    --destructive-foreground: oklch(0.577 0.245 27.325);
                    --border: oklch(0.922 0 0);
                    --input: oklch(0.922 0 0);
                    --ring: oklch(0.708 0 0);
                    --chart-1: oklch(0.646 0.222 41.116);
                    --chart-2: oklch(0.6 0.118 184.704);
                    --chart-3: oklch(0.398 0.07 227.392);
                    --chart-4: oklch(0.828 0.189 84.429);
                    --chart-5: oklch(0.769 0.188 70.08);
                    --radius: 0.625rem;
                }
                .dark {
                    --background: oklch(0.145 0 0);
                    --foreground: oklch(0.985 0 0);
                    --card: oklch(0.145 0 0);
                    --card-foreground: oklch(0.985 0 0);
                    --popover: oklch(0.145 0 0);
                    --popover-foreground: oklch(0.985 0 0);
                    --primary: oklch(0.985 0 0);
                    --primary-foreground: oklch(0.205 0 0);
                    --secondary: oklch(0.269 0 0);
                    --secondary-foreground: oklch(0.985 0 0);
                    --muted: oklch(0.269 0 0);
                    --muted-foreground: oklch(0.708 0 0);
                    --accent: oklch(0.269 0 0);
                    --accent-foreground: oklch(0.985 0 0);
                    --destructive: oklch(0.396 0.141 25.723);
                    --destructive-foreground: oklch(0.637 0.237 25.331);
                    --border: oklch(0.269 0 0);
                    --input: oklch(0.269 0 0);
                    --ring: oklch(0.439 0 0);
                    --chart-1: oklch(0.488 0.243 264.376);
                    --chart-2: oklch(0.696 0.17 162.48);
                    --chart-3: oklch(0.769 0.188 70.08);
                    --chart-4: oklch(0.627 0.265 303.9);
                    --chart-5: oklch(0.645 0.246 16.439);
                }
            </style>
        `

    const fullDocument = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview</title>
    ${tailwindAndStyles}
</head>
<body>
    ${bodyContent}
</body>
</html>
    `

    iframeDoc.open()
    iframeDoc.write(fullDocument)
    iframeDoc.close()
  }

  const handleEditorChange = (value) => {
    if (value === undefined) return
    setFiles((prev) => prev.map((f) => (f.id === activeFileId ? { ...f, content: value } : f)))
  }

  const handleEditorOnMount = (editor) => {
    editorRef.current = editor
  }

  const handleCopyCode = async () => {
    await navigator.clipboard.writeText(activeFile.content)
  }

  const handleSave = () => {
    if (onSave) {
      onSave(
        files.map((f) => ({
          name: f.name,
          language: f.language,
          content: f.content
        }))
      )
    }
    setSavedFiles(files)
  }

  const handleDownload = () => {
    const blob = new Blob([activeFile.content], { type: 'text/plain' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = activeFile.name
    a.click()
    URL.revokeObjectURL(url)
    toast.info('Downloading...')
  }

  const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().then(() => setIsFullscreen(true))
    } else {
      document.exitFullscreen().then(() => setIsFullscreen(false))
    }
  }

  const handleFormat = async () => {
    if (!editorRef.current) {
      return
    }

    await editorRef.current.getAction('editor.action.formatDocument')?.run()
  }

  return (
    <div className='flex h-full flex-col bg-background'>
      <div className='flex items-center justify-between border-b border-border bg-card px-2 py-2'>
        <div className='ml-1 flex items-center gap-1'>
          <div className='flex items-center gap-2'>
            <Code2 className='h-5 w-5 text-primary' />
            <h1 className='text-lg font-semibold'>Code Editor</h1>
          </div>
          <Separator orientation='vertical' className='h-6' />
          <Badge variant='secondary' className='font-mono text-xs'>
            {name}
          </Badge>
          {hasUnsavedChanges && (
            <Badge variant='outline' className='text-xs text-amber-600 dark:text-amber-400'>
              Unsaved Changes
            </Badge>
          )}
        </div>

        <div className='flex items-center gap-1'>
          <Button
            variant='ghost'
            size='icon'
            onClick={() => setShowPreview(!showPreview)}
            disabled={!canPreview}
            title={canPreview ? 'Toggle preview' : 'Preview not available for this language'}
          >
            {showPreview ? <EyeOff className='h-4 w-4' /> : <Eye className='h-4 w-4' />}
          </Button>
          <Button variant='ghost' size='icon' onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}>
            {theme === 'dark' ? <Sun className='h-4 w-4' /> : <Moon className='h-4 w-4' />}
          </Button>
          <Button variant='ghost' size='icon' onClick={toggleFullscreen}>
            {isFullscreen ? <Minimize2 className='h-4 w-4' /> : <Maximize2 className='h-4 w-4' />}
          </Button>
        </div>
      </div>

      <div className='flex items-center justify-between border-b border-border bg-muted/30 px-2 py-2'>
        <div className='flex items-center gap-1'>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant='ghost' size='sm' className='h-8 bg-transparent'>
                File
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align='start' className='w-56'>
              <DropdownMenuItem onClick={handleSave} className='flex items-center justify-between'>
                Save
                <Kbd>⌘ S</Kbd>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>

          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant='ghost' size='sm' className='h-8 bg-transparent'>
                Edit
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align='start' className='w-56'>
              <DropdownMenuSub>
                <DropdownMenuSubTrigger>
                  <span>Font Size</span>
                </DropdownMenuSubTrigger>
                <DropdownMenuSubContent>
                  {FONT_SIZES.map((size) => (
                    <DropdownMenuItem key={size} onClick={() => setFontSize(size)} className='justify-between'>
                      {size}px
                      {fontSize === size && <span className='text-primary'>✓</span>}
                    </DropdownMenuItem>
                  ))}
                </DropdownMenuSubContent>
              </DropdownMenuSub>
            </DropdownMenuContent>
          </DropdownMenu>

          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant='ghost' size='sm' className='h-8 bg-transparent'>
                View
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align='start' className='w-56'>
              <DropdownMenuCheckboxItem checked={lineNumbers} onCheckedChange={setLineNumbers}>
                Line Numbers
              </DropdownMenuCheckboxItem>
              <DropdownMenuCheckboxItem checked={minimap} onCheckedChange={setMinimap}>
                Mini Map
              </DropdownMenuCheckboxItem>
              <DropdownMenuCheckboxItem checked={wordWrap} onCheckedChange={setWordWrap}>
                Word Wrap
              </DropdownMenuCheckboxItem>
            </DropdownMenuContent>
          </DropdownMenu>

          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant='ghost' size='sm' className='h-8 bg-transparent'>
                Code
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align='start' className='w-56'>
              <DropdownMenuItem onClick={handleFormat} className='flex items-center justify-between'>
                Format Document
                <Kbd>⇧ ⌥ F</Kbd>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        <div className='flex items-center gap-1'>
          <Button variant='ghost' size='sm' onClick={handleCopyCode} className='h-8 bg-transparent'>
            <Copy className='h-3.5 w-3.5' />
          </Button>
          <Button variant='ghost' size='sm' onClick={handleDownload} className='h-8 bg-transparent'>
            <Download className='h-3.5 w-3.5' />
          </Button>
        </div>
      </div>

      <div className='flex items-center gap-1 border-b border-border bg-muted/20 px-2'>
        {files.map((file) => (
          <button
            key={file.id}
            onClick={() => setActiveFileId(file.id)}
            className={cn('group flex items-center gap-2 rounded-t-md px-3 py-2 text-sm transition-colors', {
              'bg-background text-foreground': file.id === activeFileId,
              'text-muted-foreground hover:bg-muted/50 hover:text-foreground': file.id !== activeFileId
            })}
          >
            <FileCode className='h-3.5 w-3.5' />
            <span className='font-mono'>{file.name}</span>
          </button>
        ))}
      </div>

      <div className='flex flex-1 overflow-hidden'>
        <div
          className={cn('flex flex-col transition-all', {
            'w-1/2': showPreview,
            'w-full': !showPreview
          })}
        >
          <Editor
            height='100%'
            language={activeFile.language}
            value={activeFile.content}
            onMount={handleEditorOnMount}
            onChange={handleEditorChange}
            theme={theme === 'dark' ? 'vs-dark' : 'light'}
            options={{
              fontSize,
              lineNumbers: lineNumbers ? 'on' : 'off',
              minimap: { enabled: minimap },
              wordWrap: wordWrap ? 'on' : 'off',
              scrollBeyondLastLine: false,
              automaticLayout: true,
              tabSize: 2,
              fontFamily: 'Geist Mono, monospace',
              fontLigatures: true,
              cursorBlinking: 'smooth',
              cursorSmoothCaretAnimation: 'on',
              smoothScrolling: true,
              padding: { top: 16, bottom: 16 },
              bracketPairColorization: { enabled: true },
              guides: {
                bracketPairs: true,
                indentation: true
              }
            }}
          />
        </div>

        {showPreview && (
          <>
            <Separator orientation='vertical' className='h-full' />
            <div className='flex w-1/2 flex-col bg-background'>
              <div className='flex items-center justify-between border-b border-border bg-muted/30 px-4 py-2'>
                <div className='flex items-center gap-2'>
                  <Eye className='h-4 w-4 text-muted-foreground' />
                  <span className='text-sm font-medium'>Preview</span>
                </div>
                <Badge variant='outline' className='text-xs'>
                  Live
                </Badge>
              </div>
              <div className='flex-1 overflow-auto bg-white'>
                <iframe
                  ref={previewRef}
                  title='Code Preview'
                  className='h-full w-full border-0'
                  sandbox='allow-scripts allow-modals allow-forms allow-popups allow-same-origin'
                />
              </div>
            </div>
          </>
        )}
      </div>

      <div className='flex items-center justify-between border-t border-border bg-muted/30 px-4 py-1.5 text-xs text-muted-foreground'>
        <div className='flex items-center gap-4'>
          <span className='font-mono'>{activeFile.language.toUpperCase()}</span>
          <span>UTF-8</span>
          <span>LF</span>
        </div>
        <div className='flex items-center gap-4'>
          <span>Lines: {activeFile.content.split('\n').length}</span>
          <span>Characters: {activeFile.content.length}</span>
        </div>
      </div>
    </div>
  )
}
