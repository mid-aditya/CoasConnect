import { useCallback, useEffect, useState } from 'react'
import {
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native'
import { StatusBar } from 'expo-status-bar'
import {
  API_URL,
  createUser,
  deleteUser,
  getHealth,
  getToken,
  getUsers,
  setToken,
  type Health,
  type User,
} from './src/api/client'
import { colors } from './src/theme'
import AuthScreen from './src/AuthScreen'

export default function App() {
  const [authed, setAuthed] = useState(() => getToken() !== null)
  const [health, setHealth] = useState<Health | null>(null)
  const [users, setUsers] = useState<User[]>([])
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [refreshing, setRefreshing] = useState(false)

  const load = useCallback(async () => {
    try {
      const [h, u] = await Promise.all([getHealth(), getUsers()])
      setHealth(h)
      setUsers(u.data)
    } catch {
      setHealth(null)
      setUsers([])
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [])

  useEffect(() => {
    load()
  }, [load])

  const onRefresh = () => {
    setRefreshing(true)
    load()
  }

  const onSubmit = async () => {
    if (!name.trim() || !email.trim()) {
      Alert.alert('Form belum lengkap', 'Nama dan email wajib diisi.')
      return
    }
    setSaving(true)
    try {
      await createUser({ name: name.trim(), email: email.trim() })
      setName('')
      setEmail('')
      await load()
    } catch (e) {
      Alert.alert('Gagal menyimpan', e instanceof Error ? e.message : 'Terjadi kesalahan.')
    } finally {
      setSaving(false)
    }
  }

  const onDelete = (u: User) => {
    Alert.alert('Hapus anggota?', `${u.name} akan dihapus dari jaringan.`, [
      { text: 'Batal', style: 'cancel' },
      {
        text: 'Hapus',
        style: 'destructive',
        onPress: async () => {
          try {
            await deleteUser(u.id)
            await load()
          } catch (e) {
            Alert.alert('Gagal menghapus', e instanceof Error ? e.message : 'Terjadi kesalahan.')
          }
        },
      },
    ])
  }

  const onLogout = () => {
    setToken(null)
    setAuthed(false)
  }

  if (!authed) {
    return <AuthScreen onAuthed={() => setAuthed(true)} />
  }

  return (
    <KeyboardAvoidingView
      style={styles.root}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <StatusBar style="light" />
      <ScrollView
        contentContainerStyle={styles.content}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.aqua400} />
        }
      >
        {/* Header */}
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
          <Pressable onPress={onLogout} hitSlop={8}>
            <Text style={styles.logout}>Keluar</Text>
          </Pressable>
        </View>

        {/* Status API */}
        <View style={[styles.card, styles.darkCard]}>
          <View style={styles.cardRow}>
            <Text style={styles.cardLabel}>Status layanan</Text>
            {loading ? (
              <ActivityIndicator color={colors.aqua400} size="small" />
            ) : health ? (
              <View style={styles.statusPill}>
                <View style={styles.statusDot} />
                <Text style={styles.statusText}>{health.status}</Text>
              </View>
            ) : (
              <Text style={styles.statusOffline}>offline</Text>
            )}
          </View>
          {health ? (
            <Text style={styles.cardDetail}>
              {health.service} · uptime {Math.floor(health.uptime_s / 60)} m {health.uptime_s % 60} d
            </Text>
          ) : (
            <Text style={styles.cardDetail}>API tidak merespons di {API_URL}</Text>
          )}
        </View>

        {/* Form tambah */}
        <View style={[styles.card, styles.lightCard]}>
          <Text style={styles.lightTitle}>Tambah anggota</Text>
          <TextInput
            style={styles.input}
            placeholder="Nama lengkap"
            placeholderTextColor={colors.inkMuted}
            value={name}
            onChangeText={setName}
          />
          <TextInput
            style={styles.input}
            placeholder="Email"
            placeholderTextColor={colors.inkMuted}
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
          />
          <Pressable
            style={({ pressed }) => [styles.primaryBtn, pressed && styles.btnPressed]}
            onPress={onSubmit}
            disabled={saving}
          >
            {saving ? (
              <ActivityIndicator color={colors.ink950} size="small" />
            ) : (
              <Text style={styles.primaryBtnText}>Tambah ke jaringan</Text>
            )}
          </Pressable>
        </View>

        {/* Daftar anggota */}
        <View style={[styles.card, styles.lightCard]}>
          <Text style={styles.lightTitle}>
            Anggota jaringan{' '}
            <Text style={styles.count}>{users.length > 0 ? `· ${users.length}` : ''}</Text>
          </Text>

          {!loading && users.length === 0 ? (
            <Text style={styles.empty}>Belum ada anggota. Tambahkan yang pertama di atas.</Text>
          ) : (
            users.map((u) => (
              <View key={u.id} style={styles.member}>
                <View style={styles.avatar}>
                  <Text style={styles.avatarText}>{u.name.charAt(0).toUpperCase()}</Text>
                </View>
                <View style={styles.memberCopy}>
                  <Text style={styles.memberName} numberOfLines={1}>
                    {u.name}
                  </Text>
                  <Text style={styles.memberEmail} numberOfLines={1}>
                    {u.email}
                  </Text>
                </View>
                <Pressable onPress={() => onDelete(u)} hitSlop={8}>
                  <Text style={styles.delete}>Hapus</Text>
                </Pressable>
              </View>
            ))
          )}
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  )
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: colors.ink950,
  },
  content: {
    padding: 20,
    paddingTop: 64,
    paddingBottom: 48,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    marginBottom: 24,
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
  logout: {
    color: colors.coral400,
    fontSize: 13,
    fontWeight: '600',
  },
  card: {
    borderRadius: 16,
    padding: 18,
    marginBottom: 16,
  },
  darkCard: {
    backgroundColor: colors.ink900,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
  },
  lightCard: {
    backgroundColor: colors.sand100,
  },
  cardRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  cardLabel: {
    color: colors.sand100,
    fontSize: 15,
    fontWeight: '600',
  },
  statusPill: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 999,
    backgroundColor: 'rgba(62, 224, 200, 0.12)',
  },
  statusDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: colors.aqua400,
  },
  statusText: { color: colors.aqua400, fontSize: 12, fontWeight: '600', textTransform: 'uppercase' },
  statusOffline: {
    color: colors.coral400,
    fontSize: 12,
    fontWeight: '600',
    textTransform: 'uppercase',
  },
  cardDetail: {
    color: colors.textDim,
    fontSize: 13,
    marginTop: 10,
  },
  lightTitle: {
    color: colors.ink900,
    fontSize: 16,
    fontWeight: '700',
    marginBottom: 14,
  },
  count: { color: colors.ocean700 },
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
  empty: {
    color: colors.inkMuted,
    fontSize: 14,
    lineHeight: 20,
  },
  member: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    paddingVertical: 12,
    borderTopWidth: 1,
    borderTopColor: 'rgba(11, 27, 40, 0.06)',
  },
  avatar: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: colors.ink900,
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: {
    color: colors.sand100,
    fontSize: 16,
    fontWeight: '700',
  },
  memberCopy: { flex: 1 },
  memberName: {
    color: colors.ink900,
    fontSize: 15,
    fontWeight: '600',
  },
  memberEmail: {
    color: colors.inkMuted,
    fontSize: 13,
    marginTop: 2,
  },
  delete: {
    color: colors.coral500,
    fontSize: 13,
    fontWeight: '600',
  },
})
