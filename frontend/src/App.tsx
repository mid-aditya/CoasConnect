import { useEffect, useState } from 'react'
import Navbar from './components/Navbar'
import Hero from './components/Hero'
import ApiStatus from './components/ApiStatus'
import Dashboard from './components/Dashboard'
import Login from './components/Login'
import Features from './components/Features'
import Footer from './components/Footer'
import { getMe, getToken, setToken, type User } from './api/client'

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
    <div className="min-h-screen bg-sand-100">
      <Navbar />
      <main>
        <Hero />
        <ApiStatus />
        {!checking &&
          (user ? (
            <Dashboard
              user={user}
              onLogout={() => {
                setToken(null)
                setUser(null)
              }}
            />
          ) : (
            <Login
              onAuthed={async () => {
                const res = await getMe()
                setUser(res.data)
              }}
            />
          ))}
        <Features />
      </main>
      <Footer />
    </div>
  )
}
