import { useState } from 'react'
import Navbar from './components/Navbar'
import Hero from './components/Hero'
import ApiStatus from './components/ApiStatus'
import UserManager from './components/UserManager'
import Login from './components/Login'
import Features from './components/Features'
import Footer from './components/Footer'
import { getToken, setToken } from './api/client'

export default function App() {
  const [authed, setAuthed] = useState(() => getToken() !== null)

  return (
    <div className="min-h-screen bg-sand-100">
      <Navbar />
      <main>
        <Hero />
        <ApiStatus />
        {authed ? (
          <UserManager
            onLogout={() => {
              setToken(null)
              setAuthed(false)
            }}
          />
        ) : (
          <Login onAuthed={() => setAuthed(true)} />
        )}
        <Features />
      </main>
      <Footer />
    </div>
  )
}
