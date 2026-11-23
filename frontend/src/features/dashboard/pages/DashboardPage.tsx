import { useAuth } from '../../auth/useAuth'
import { HealthStatus } from '../components/HealthStatus'
import { ChatWidget } from '../components/ChatWidget'
import { LogOut, Rocket } from 'lucide-react'

export function DashboardPage() {
  const { user, logout } = useAuth()

  return (
    <div className="flex flex-col h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      {/* Header / Navbar */}
      <header className="border-b border-slate-200 bg-white shadow-sm flex-shrink-0">
        <div className="px-4 md:px-6 py-3 flex items-center justify-between gap-6">
          {/* Logo */}
          <div className="flex items-center gap-3 flex-shrink-0">
            <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
              <Rocket className="w-6 h-6 text-white" />
            </div>
            <h1 className="text-xl md:text-2xl font-bold text-slate-900">Companion</h1>
          </div>

          {/* User Actions */}
          <div className="flex items-center gap-6 flex-shrink-0">
            <div className="text-right hidden sm:block">
              <p className="text-xs md:text-sm font-medium text-slate-900">{user?.email}</p>
              <div>
                <HealthStatus />
              </div>
            </div>
            <button
              onClick={logout}
              className="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all duration-200 text-xs md:text-sm font-medium flex-shrink-0 hover:shadow-sm"
              title="Logout"
            >
              <LogOut className="w-4 h-4" />
              <span className="hidden sm:inline">Logout</span>
            </button>
          </div>
        </div>
      </header>

      {/* Chat Widget - Full Height */}
      <main className="flex-1 overflow-hidden p-4 md:p-6">
        <ChatWidget />
      </main>
    </div>
  )
}




