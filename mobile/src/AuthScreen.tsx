import { useState } from 'react'
import {
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native'
import { StatusBar } from 'expo-status-bar'
import { login, register, setToken, API_URL } from './api/client'
import { colors } from './theme'

export default function AuthScreen({ onAuthed }: { onAuthed: () => void }) {
  const [mode, setMode] = useState<'login' | 'register'>('login')
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [busy, setBusy] = useState(false)

  const onSubmit = async () => {
    if (!email.trim() || !password) {
      Alert.alert('Form belum lengkap', 'Email dan password wajib diisi.')
      return
    }
    if (mode === 'register' && !name.trim()) {
      Alert.alert('Form belum lengkap', 'Nama wajib diisi untuk registrasi.')
      return
    }
    setBusy(true)
    try {
      const res =
        mode === 'login'
          ? await login(email.trim(), password)
          : await register(name.trim(), email.trim(), password)
      setToken(res.data.token)
      onAuthed()
    } catch (e) {
      Alert.alert(mode === 'login' ? 'Gagal masuk' : 'Gagal daftar', e instanceof Error ? e.message : 'Terjadi kesalahan.')
    } finally {
      setBusy(false)
    }
  }

  return (
    <KeyboardAvoidingView
      style={styles.root}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <StatusBar style="light" />
      <View style={styles.content}>
        <View style={styles.header}>
          <View style={styles.logo}>
            <Text style={styles.logoText}>C</Text>
          </View>
          <View style={styles.headerCopy}>
            <Text style={styles.brand}>
              Coas<Text style={styles.brandAccent}>Connect</Text>
            </Text>
            <Text style={styles.tagline}>Jaringan komunitas pesisir</Text>
          </View>
        </View>

        <View style={styles.card}>
          <Text style={styles.title}>{mode === 'login' ? 'Masuk ke jaringan' : 'Daftar ke jaringan'}</Text>

          {mode === 'register' && (
            <TextInput
              style={styles.input}
              placeholder="Nama lengkap"
              placeholderTextColor={colors.inkMuted}
              value={name}
              onChangeText={setName}
            />
          )}
          <TextInput
            style={styles.input}
            placeholder="Email"
            placeholderTextColor={colors.inkMuted}
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
          />
          <TextInput
            style={styles.input}
            placeholder={mode === 'register' ? 'Password (min. 8 karakter)' : 'Password'}
            placeholderTextColor={colors.inkMuted}
            value={password}
            onChangeText={setPassword}
            secureTextEntry
            autoCapitalize="none"
          />

          <Pressable
            style={({ pressed }) => [styles.primaryBtn, pressed && styles.btnPressed]}
            onPress={onSubmit}
            disabled={busy}
          >
            {busy ? (
              <ActivityIndicator color={colors.ink950} size="small" />
            ) : (
              <Text style={styles.primaryBtnText}>{mode === 'login' ? 'Masuk' : 'Daftar'}</Text>
            )}
          </Pressable>

          <Pressable onPress={() => setMode(mode === 'login' ? 'register' : 'login')}>
            <Text style={styles.toggle}>
              {mode === 'login' ? 'Belum punya akun? Daftar' : 'Sudah punya akun? Masuk'}
            </Text>
          </Pressable>
        </View>

        <Text style={styles.hint}>API: {API_URL}</Text>
      </View>
    </KeyboardAvoidingView>
  )
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: colors.ink950,
  },
  content: {
    flex: 1,
    justifyContent: 'center',
    padding: 20,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    marginBottom: 32,
  },
  logo: {
    width: 44,
    height: 44,
    borderRadius: 12,
    backgroundColor: 'rgba(34, 199, 176, 0.15)',
    borderWidth: 1,
    borderColor: 'rgba(62, 224, 200, 0.4)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoText: {
    color: colors.aqua400,
    fontFamily: Platform.select({ ios: 'Georgia', android: 'serif' }),
    fontSize: 24,
    fontWeight: '700',
  },
  headerCopy: { flex: 1 },
  brand: {
    color: colors.sand100,
    fontSize: 22,
    fontWeight: '700',
  },
  brandAccent: { color: colors.aqua400 },
  tagline: {
    color: colors.textDim,
    fontSize: 13,
    marginTop: 2,
  },
  card: {
    backgroundColor: colors.sand100,
    borderRadius: 16,
    padding: 18,
  },
  title: {
    color: colors.ink900,
    fontSize: 16,
    fontWeight: '700',
    marginBottom: 14,
  },
  input: {
    backgroundColor: colors.white,
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 12,
    fontSize: 15,
    color: colors.ink900,
    marginBottom: 10,
  },
  primaryBtn: {
    backgroundColor: colors.aqua500,
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 4,
  },
  btnPressed: { opacity: 0.85, transform: [{ scale: 0.99 }] },
  primaryBtnText: {
    color: colors.ink950,
    fontSize: 15,
    fontWeight: '700',
  },
  toggle: {
    color: colors.ocean700,
    fontSize: 14,
    fontWeight: '600',
    textAlign: 'center',
    marginTop: 14,
  },
  hint: {
    color: colors.textFaint,
    fontSize: 12,
    textAlign: 'center',
    marginTop: 16,
  },
})
