import { useState, useRef, useEffect } from 'react'
import { Send, Loader } from 'lucide-react'

interface Message {
  id: string
  type: 'user' | 'assistant'
  content: string
}

function generateThreadId(): string {
  if (typeof window !== 'undefined' && window.crypto?.randomUUID) {
    return crypto.randomUUID()
  }
  return `${Date.now()}_${Math.random().toString(36).substring(2, 9)}`
}

export function ChatWidget() {
  const [messages, setMessages] = useState<Message[]>([])
  const [input, setInput] = useState('')
  const [isLoading, setIsLoading] = useState(false)
  const [threadId] = useState(() => generateThreadId())
  const messagesEndRef = useRef<HTMLDivElement>(null)
  const inputRef = useRef<HTMLInputElement>(null)

  const scrollToBottom = () => {
    if (messagesEndRef.current && typeof messagesEndRef.current.scrollIntoView === 'function') {
      messagesEndRef.current.scrollIntoView({ behavior: 'smooth' })
    }
  }

  useEffect(() => {
    scrollToBottom()
  }, [messages])

  useEffect(() => {
    if (!isLoading) {
      inputRef.current?.focus()
    }
  }, [isLoading])

  const handleSendMessage = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!input.trim() || isLoading) {
      return
    }

    const messageText = input
    const userMessage: Message = {
      id: `msg_${Date.now()}`,
      type: 'user',
      content: messageText,
    }

    setMessages((prev) => [...prev, userMessage])
    setInput('')
    setIsLoading(true)

    try {
      const response = await fetch(
        `${import.meta.env.VITE_API_URL || 'http://localhost:8080'}/api/chat`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          credentials: 'include',
          body: JSON.stringify({
            message: messageText,
            thread_id: threadId,
          }),
        },
      )

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }

      const assistantMessageId = `msg_${Date.now()}_assistant`
      let assistantContent = ''

      if (response.body) {
        const reader = response.body.getReader()
        const decoder = new TextDecoder()

        // Add empty assistant message first
        setMessages((prev) => [
          ...prev,
          {
            id: assistantMessageId,
            type: 'assistant',
            content: '',
          },
        ])

        try {
          while (true) {
            const { done, value } = await reader.read()
            if (done) break

            const chunk = decoder.decode(value, { stream: true })
            const lines = chunk.split('\n')

            for (const line of lines) {
              if (line.startsWith('data: ')) {
                try {
                  const data = JSON.parse(line.slice(6))
                  if (data.type === 'text' && data.content) {
                    assistantContent += data.content
                    setMessages((prev) => {
                      const updated = [...prev]
                      const lastMsg = updated[updated.length - 1]
                      if (lastMsg && lastMsg.id === assistantMessageId) {
                        lastMsg.content = assistantContent
                      }
                      return updated
                    })
                  }
                } catch {
                  // Skip invalid JSON lines
                }
              }
            }
          }
        } finally {
          reader.releaseLock()
        }
      }
    } catch (error) {
      const errorMessage: Message = {
        id: `msg_${Date.now()}_error`,
        type: 'assistant',
        content: `❌ Fehler: ${error instanceof Error ? error.message : 'Verbindung fehlgeschlagen'}`,
      }
      setMessages((prev) => [...prev, errorMessage])
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <div className="flex flex-col h-full bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
      {/* Messages Area */}
      <div className="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 bg-gradient-to-b from-slate-50 to-white">
        {messages.length === 0 ? (
          <div className="flex items-center justify-center h-full text-center">
            <div>
              <p className="text-4xl mb-3">👋</p>
              <p className="text-sm md:text-base text-slate-600">Start a conversation</p>
            </div>
          </div>
        ) : (
          messages.map((message) => (
            <div key={message.id} className={`flex ${message.type === 'user' ? 'justify-end' : 'justify-start'}`}>
              <div
                className={`max-w-xs sm:max-w-sm md:max-w-md px-4 py-2 rounded-lg text-sm break-words ${
                  message.type === 'user'
                    ? 'bg-blue-500 text-white rounded-br-none'
                    : 'bg-slate-100 text-slate-900 border border-slate-200 rounded-bl-none'
                }`}
              >
                {message.content}
              </div>
            </div>
          ))
        )}
        {isLoading && (
          <div className="flex justify-start">
            <div className="bg-slate-100 border border-slate-200 text-slate-900 rounded-lg rounded-bl-none px-4 py-2">
              <div className="flex items-center gap-2">
                <div className="w-2 h-2 bg-slate-400 rounded-full animate-bounce" />
                <div className="w-2 h-2 bg-slate-400 rounded-full animate-bounce delay-100" />
                <div className="w-2 h-2 bg-slate-400 rounded-full animate-bounce delay-200" />
              </div>
            </div>
          </div>
        )}
        <div ref={messagesEndRef} />
      </div>

      {/* Input Area */}
      <div className="border-t border-slate-200 bg-white p-4 md:p-6 flex-shrink-0">
        <form onSubmit={handleSendMessage} className="flex gap-2">
          <input
            ref={inputRef}
            type="text"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            placeholder="Enter a message..."
            disabled={isLoading}
            className="flex-1 px-4 py-2 md:py-3 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-slate-100 disabled:cursor-not-allowed transition-all"
          />
          <button
            type="submit"
            disabled={isLoading || !input.trim()}
            className="px-4 py-2 md:py-3 bg-blue-500 hover:bg-blue-600 disabled:bg-slate-300 disabled:cursor-not-allowed text-white rounded-lg transition-colors font-medium text-sm flex-shrink-0 flex items-center justify-center gap-2"
          >
            {isLoading ? <Loader className="w-4 h-4 animate-spin" /> : <Send className="w-4 h-4" />}
            <span className="hidden sm:inline">{isLoading ? 'Sending...' : 'Send'}</span>
          </button>
        </form>
      </div>
    </div>
  )
}


