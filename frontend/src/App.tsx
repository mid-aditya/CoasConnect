import { useEffect, useState } from 'react'
import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom'
import Navbar from './components/Navbar'
import Hero from './components/Hero'
import HowItWorks from './components/HowItWorks'
import Roles from './components/Roles'
import CampaignsSection from './components/CampaignsSection'
import Dashboard from './components/Dashboard'
import Login from './components/Login'
import Footer from './components/Footer'
import { getMe, getToken, setToken, type User } from './api/client'

function Landing({ user }: { user: User | null }) {
  return (
    <div className="min-h-screen bg-paper">
      <Navbar authed={!!user} />
      <main>
        <Hero />
        <HowItWorks />
        <Roles />
        <CampaignsSection />
      </main>
      <Footer />
    </div>
  )
}

export default function App() {
  const [user, setUser] = useState<User | null>(null)
  const [checking, setChecking] = useState(true)

  useEffect(() => {
    if (!getToken()) {
      setChecking(false)
      return
    }
    getMe()
      .then((res) => setUser(res.data))
      .catch(() => setToken(null))
      .finally(() => setChecking(false))
  }, [])

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Landing user={user} />} />
        <Route
          path="/login"
          element={
            checking ? null : user ? (
              <Navigate to="/app" replace />
            ) : (
              <Login
                onAuthed={async () => {
                  const res = await getMe()
                  setUser(res.data)
                }}
              />
            )
          }
        />
        <Route
          path="/app"
          element={
            checking ? null : user ? (
              <Dashboard
                user={user}
                onLogout={() => {
                  setToken(null)
                  setUser(null)
                }}
              />
            ) : (
              <Navigate to="/login" replace />
            )
          }
        />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </BrowserRouter>
  )
}
