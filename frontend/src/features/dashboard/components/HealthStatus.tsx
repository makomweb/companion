import { useEffect, useState } from 'react'
import { RefreshCw, Loader } from 'lucide-react'

interface HealthData {
  status: string
  version: string
  timestamp: string
}

export function HealthStatus() {
  const [health, setHealth] = useState<HealthData | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const fetchHealth = async () => {
    setLoading(true)
    setError(null)
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8080'
    try {
      const response = await fetch(`${apiUrl}/api/health`)
      if (!response.ok) throw new Error(`HTTP ${response.status}`)
      const data = await response.json()
      setHealth(data)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error')
      setHealth(null)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchHealth()
    const interval = setInterval(fetchHealth, 10000)
    return () => clearInterval(interval)
  }, [])

  const isHealthy = !error && health?.status === 'ok'
  const statusText = loading ? 'checking...' : isHealthy ? '✓ online' : '✗ offline'

  return (
    <div className="flex items-center justify-between text-xs text-slate-500 hover:text-slate-700 transition-colors">
      <span>Backend</span><span>{statusText}</span>
      <button
        onClick={fetchHealth}
        disabled={loading}
        className="p-1 hover:bg-slate-100 rounded transition-colors disabled:opacity-50"
        title="Refresh health status"
      >
        <RefreshCw className={`w-3 h-3 ${loading ? 'animate-spin' : ''}`} />
      </button>
    </div>
  )
}


