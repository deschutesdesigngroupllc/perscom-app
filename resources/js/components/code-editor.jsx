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
import {
  AlertCircle,
  AlertTriangle,
  Code2,
  Copy,
  Download,
  Eye,
  EyeOff,
  FileCode,
  Info,
  Maximize2,
  Minimize2,
  Moon,
  Sun,
  Terminal,
  Trash2
} from 'lucide-react'
import { useEffect, useRef, useState } from 'react'
import { toast } from 'sonner'

const FONT_SIZES = [10, 12, 14, 16, 18, 20, 24]

export function CodeEditor({ html, onSave, defaultHtml, previewUrl }) {
  const { name, csrf_token: csrfToken } = usePage().props
  const previewRef = useRef(null)
  const editorRef = useRef(null)
  const formRef = useRef(null)
  const debounceRef = useRef(null)
  const [activeFileId, setActiveFileId] = useState(1)
  const [theme, setTheme] = useState('dark')
  const [fontSize, setFontSize] = useState(14)
  const [lineNumbers, setLineNumbers] = useState(true)
  const [minimap, setMinimap] = useState(false)
  const [wordWrap, setWordWrap] = useState(false)
  const [isFullscreen, setIsFullscreen] = useState(false)
  const [showPreview, setShowPreview] = useState(false)
  const [showConsole, setShowConsole] = useState(false)
  const [consoleLogs, setConsoleLogs] = useState([])
  const [consoleHeight, setConsoleHeight] = useState(192)
  const [isDragging, setIsDragging] = useState(false)
  const [editorWidth, setEditorWidth] = useState(50)
  const isDraggingConsole = useRef(false)
  const isDraggingDivider = useRef(false)
  const consoleStartY = useRef(0)
  const consoleStartHeight = useRef(0)
  const dividerStartX = useRef(0)
  const dividerStartWidth = useRef(0)
  const containerRef = useRef(null)
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
      if (debounceRef.current) {
        clearTimeout(debounceRef.current)
      }
      debounceRef.current = setTimeout(() => {
        updatePreview()
      }, 500)
    }

    return () => {
      if (debounceRef.current) {
        clearTimeout(debounceRef.current)
      }
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

  useEffect(() => {
    const handleMessage = (event) => {
      if (event.data?.type === 'console') {
        setConsoleLogs((prev) => [
          ...prev,
          {
            id: Date.now(),
            level: event.data.level,
            message: event.data.message,
            timestamp: event.data.timestamp
          }
        ])
      }
    }

    window.addEventListener('message', handleMessage)

    return () => {
      window.removeEventListener('message', handleMessage)
    }
  }, [])

  useEffect(() => {
    const handleMouseMove = (e) => {
      if (isDraggingConsole.current) {
        const delta = consoleStartY.current - e.clientY
        const newHeight = Math.min(Math.max(consoleStartHeight.current + delta, 100), 600)
        setConsoleHeight(newHeight)
      }

      if (isDraggingDivider.current && containerRef.current) {
        const containerRect = containerRef.current.getBoundingClientRect()
        const newWidth = ((e.clientX - containerRect.left) / containerRect.width) * 100
        setEditorWidth(Math.min(Math.max(newWidth, 20), 80))
      }
    }

    const handleMouseUp = () => {
      if (isDraggingConsole.current || isDraggingDivider.current) {
        isDraggingConsole.current = false
        isDraggingDivider.current = false
        setIsDragging(false)
        document.body.style.cursor = ''
        document.body.style.userSelect = ''
      }
    }

    window.addEventListener('mousemove', handleMouseMove)
    window.addEventListener('mouseup', handleMouseUp)

    return () => {
      window.removeEventListener('mousemove', handleMouseMove)
      window.removeEventListener('mouseup', handleMouseUp)
    }
  }, [])

  const handleConsoleResizeStart = (e) => {
    e.preventDefault()
    isDraggingConsole.current = true
    setIsDragging(true)
    consoleStartY.current = e.clientY
    consoleStartHeight.current = consoleHeight
    document.body.style.cursor = 'ns-resize'
    document.body.style.userSelect = 'none'
  }

  const handleDividerResizeStart = (e) => {
    e.preventDefault()
    isDraggingDivider.current = true
    setIsDragging(true)
    dividerStartX.current = e.clientX
    dividerStartWidth.current = editorWidth
    document.body.style.cursor = 'ew-resize'
    document.body.style.userSelect = 'none'
  }

  const updatePreview = () => {
    if (!previewUrl || !formRef.current) return

    const htmlFile = files.find((f) => f.name === 'index.html')
    const htmlInput = formRef.current.querySelector('input[name="html"]')

    if (htmlInput) {
      htmlInput.value = htmlFile?.content || ''
      formRef.current.submit()
    }
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

      <div ref={containerRef} className='flex flex-1 overflow-hidden'>
        <div className='flex flex-col' style={{ width: showPreview ? `${editorWidth}%` : '100%' }}>
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
            <div
              onMouseDown={handleDividerResizeStart}
              className='group flex w-1 cursor-ew-resize items-center justify-center bg-border hover:bg-primary/50'
            >
              <div className='h-8 w-0.5 rounded-full bg-muted-foreground/30 group-hover:bg-primary' />
            </div>
            <div className='flex flex-col bg-background' style={{ width: `${100 - editorWidth}%` }}>
              <div className='flex items-center justify-between border-b border-border bg-muted/30 px-4 py-2'>
                <div className='flex items-center gap-2'>
                  <Eye className='h-4 w-4 text-muted-foreground' />
                  <span className='text-sm font-medium'>Preview</span>
                </div>
                <div className='flex items-center gap-2'>
                  <Button variant='ghost' size='sm' onClick={() => setShowConsole(!showConsole)} className='h-7 gap-1.5 px-2'>
                    <Terminal className='size-3.5 text-muted-foreground' />
                    <span className='text-xs font-medium'>Console</span>
                    {consoleLogs.length > 0 && (
                      <Badge
                        variant={consoleLogs.some((log) => log.level === 'error') ? 'destructive' : 'secondary'}
                        className='h-4 min-w-4 px-1 text-[10px]'
                      >
                        {consoleLogs.length}
                      </Badge>
                    )}
                  </Button>
                  <Badge variant='outline' className='text-xs'>
                    Live
                  </Badge>
                </div>
              </div>
              <div className='min-h-0 flex-1 overflow-auto bg-white'>
                <form ref={formRef} action={previewUrl} method='POST' target='preview-iframe' className='hidden'>
                  <input type='hidden' name='_token' value={csrfToken || ''} />
                  <input type='hidden' name='html' value='' />
                </form>
                <iframe
                  ref={previewRef}
                  name='preview-iframe'
                  title='Code Preview'
                  className={cn('h-full w-full border-0', { 'pointer-events-none': isDragging })}
                  sandbox='allow-scripts allow-modals allow-forms allow-popups allow-same-origin'
                />
              </div>
              {showConsole && (
                <div className='flex flex-shrink-0 flex-col' style={{ height: consoleHeight }}>
                  <div
                    onMouseDown={handleConsoleResizeStart}
                    className='group flex h-1 cursor-ns-resize items-center justify-center border-t border-border bg-muted/30 hover:bg-muted/50'
                  >
                    <div className='h-0.5 w-8 rounded-full bg-muted-foreground/30 group-hover:bg-muted-foreground/50' />
                  </div>
                  <div className='flex items-center justify-between border-b border-border bg-muted/30 px-3 py-1.5'>
                    <div className='flex items-center gap-2'>
                      <Terminal className='size-4 text-muted-foreground' />
                      <span className='text-sm font-medium'>Console</span>
                    </div>
                    <Button variant='ghost' size='sm' onClick={() => setConsoleLogs([])} className='h-6 px-2' title='Clear console'>
                      <Trash2 className='h-3 w-3' />
                    </Button>
                  </div>
                  <div className='flex-1 overflow-auto bg-zinc-950 p-2 font-mono text-xs'>
                    {consoleLogs.length === 0 ? (
                      <div className='text-zinc-500'>No console output</div>
                    ) : (
                      consoleLogs.map((log) => (
                        <div
                          key={log.id}
                          className={cn('flex items-start gap-2 py-0.5', {
                            'text-zinc-300': log.level === 'log',
                            'text-yellow-400': log.level === 'warn',
                            'text-red-400': log.level === 'error',
                            'text-blue-400': log.level === 'info',
                            'text-zinc-500': log.level === 'debug'
                          })}
                        >
                          {log.level === 'error' && <AlertCircle className='mt-0.5 h-3 w-3 flex-shrink-0' />}
                          {log.level === 'warn' && <AlertTriangle className='mt-0.5 h-3 w-3 flex-shrink-0' />}
                          {log.level === 'info' && <Info className='mt-0.5 h-3 w-3 flex-shrink-0' />}
                          {(log.level === 'log' || log.level === 'debug') && <span className='mt-0.5 h-3 w-3 flex-shrink-0' />}
                          <span className='whitespace-pre-wrap break-all'>{log.message}</span>
                        </div>
                      ))
                    )}
                  </div>
                </div>
              )}
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
