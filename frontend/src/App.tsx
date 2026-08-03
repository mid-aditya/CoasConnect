import Navbar from './components/Navbar'
import Hero from './components/Hero'
import ApiStatus from './components/ApiStatus'
import UserManager from './components/UserManager'
import Features from './components/Features'
import Footer from './components/Footer'

export default function App() {
  return (
    <div className="min-h-screen bg-sand-100">
      <Navbar />
      <main>
        <Hero />
        <ApiStatus />
        <UserManager />
        <Features />
      </main>
      <Footer />
    </div>
  )
}
