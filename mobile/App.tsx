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
  createAppointment,
  createCase,
  getAppointments,
  getCases,
  getHealth,
  getKoas,
  getMe,
  getToken,
  setToken,
  type Appointment,
  type CaseView,
  type Health,
  type Koas,
  type User,
} from './src/api/client'
import { colors } from './src/theme'
import AuthScreen from './src/AuthScreen'

const STATUS_COLOR: Record<string, string> = {
  aktif: colors.aqua400,
  pulih: colors.coral400,
  selesai: colors.textDim,
}

const fmt = (iso: string) => new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })

export default function App() {
  const [authed, setAuthed] = useState(() => getToken() !== null)
  const [me, setMe] = useState<User | null>(null)
  const [health, setHealth] = useState<Health | null>(null)
  const [cases, setCases] = useState<CaseView[]>([])
  const [koas, setKoas] = useState<Koas[]>([])
  const [selected, setSelected] = useState<CaseView | null>(null)
  const [appts, setAppts] = useState<Appointment[]>([])

  const [koasId, setKoasId] = useState<number | null>(null)
  const [complaint, setComplaint] = useState('')
  const [when, setWhen] = useState('')
  const [apptWhen, setApptWhen] = useState('')
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [refreshing, setRefreshing] = useState(false)

  const load = useCallback(async () => {
    try {
      const [h, c] = await Promise.all([getHealth(), getCases()])
      setHealth(h)
      setCases(c.data)
    } catch {
      setHealth(null)
      setCases([])
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [])

  useEffect(() => {
    if (!authed) return
    getMe()
      .then((r) => setMe(r.data))
      .catch(() => {})
    getKoas()
      .then((r) => setKoas(r.data))
      .catch(() => {})
    load()
  }, [authed, load])

  const onRefresh = () => {
    setRefreshing(true)
    load()
  }

  const onLogout = () => {
    setToken(null)
    setAuthed(false)
    setMe(null)
    setSelected(null)
  }

  const onSubmitCase = async () => {
    if (!koasId || !complaint.trim() || !when.trim()) {
      Alert.alert('Form belum lengkap', 'Pilih dokter koas, tulis keluhan, dan isi jadwal.')
      return
    }
    setSaving(true)
    try {
      const res = await createCase({
        koas_id: koasId,
        complaint: complaint.trim(),
        scheduled_at: new Date(when.trim()).toISOString(),
      })
      setComplaint('')
      setWhen('')
      setSelected(res.data)
      await load()
      await openCase(res.data)
    } catch (e) {
      Alert.alert('Gagal membuka kasus', e instanceof Error ? e.message : 'Terjadi kesalahan.')
    } finally {
      setSaving(false)
    }
  }

  const openCase = async (c: CaseView) => {
    setSelected(c)
    try {
      const res = await getAppointments(c.id)
      setAppts(res.data)
    } catch {
      setAppts([])
    }
  }

  const onSubmitAppt = async () => {
    if (!selected || !apptWhen.trim()) return
    setSaving(true)
    try {
      await createAppointment(selected.id, new Date(apptWhen.trim()).toISOString())
      setApptWhen('')
      await openCase(selected)
    } catch (e) {
      Alert.alert('Gagal membuat janji temu', e instanceof Error ? e.message : 'Terjadi kesalahan.')
    } finally {
      setSaving(false)
    }
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
            <Text style={styles.tagline}>
              {me ? `${me.name} · ${me.role}` : 'Monitoring pasien · dokter koas'}
            </Text>
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

        {/* Form buka kasus (pasien) */}
        <View style={[styles.card, styles.lightCard]}>
          <Text style={styles.lightTitle}>Buat janji temu baru</Text>

          <View style={styles.chips}>
            {koas.map((k) => (
              <Pressable
                key={k.id}
                onPress={() => setKoasId(k.id)}
                style={[styles.chip, koasId === k.id && styles.chipActive]}
              >
                <Text style={[styles.chipText, koasId === k.id && styles.chipTextActive]}>
                  {k.name}
                </Text>
              </Pressable>
            ))}
            {koas.length === 0 && <Text style={styles.hint}>Belum ada dokter koas terdaftar.</Text>}
          </View>

          <TextInput
            style={styles.input}
            placeholder="Keluhan awal (cth: demam sejak 2 hari)"
            placeholderTextColor={colors.inkMuted}
            value={complaint}
            onChangeText={setComplaint}
            multiline
          />
          <TextInput
            style={styles.input}
            placeholder="Jadwal temu pertama, mis. 2026-08-10T09:00"
            placeholderTextColor={colors.inkMuted}
            value={when}
            onChangeText={setWhen}
            autoCapitalize="none"
          />

          <Pressable
            style={({ pressed }) => [styles.primaryBtn, pressed && styles.btnPressed]}
            onPress={onSubmitCase}
            disabled={saving}
          >
            {saving ? (
              <ActivityIndicator color={colors.ink950} size="small" />
            ) : (
              <Text style={styles.primaryBtnText}>Buka kasus</Text>
            )}
          </Pressable>
        </View>

        {/* Daftar kasus */}
        <View style={[styles.card, styles.lightCard]}>
          <Text style={styles.lightTitle}>
            Kasus saya <Text style={styles.count}>{cases.length > 0 ? `· ${cases.length}` : ''}</Text>
          </Text>

          {!loading && cases.length === 0 ? (
            <Text style={styles.empty}>Belum ada kasus. Buka kasus pertama di atas.</Text>
          ) : (
            cases.map((c) => (
              <Pressable key={c.id} onPress={() => openCase(c)}>
                <View style={styles.member}>
                  <View style={styles.avatar}>
                    <Text style={styles.avatarText}>{c.patient_name.charAt(0).toUpperCase()}</Text>
                  </View>
                  <View style={styles.memberCopy}>
                    <Text style={styles.memberName} numberOfLines={1}>
                      {c.complaint}
                    </Text>
                    <Text style={styles.memberEmail} numberOfLines={1}>
                      {c.koas_name} · {c.supervisor_name}
                    </Text>
                  </View>
                  <Text style={[styles.statusText, { color: STATUS_COLOR[c.status] }]}>
                    {c.status}
                  </Text>
                </View>
              </Pressable>
            ))
          )}
        </View>

        {/* Detail kasus: sesi monitoring */}
        {selected && (
          <View style={[styles.card, styles.lightCard]}>
            <Text style={styles.lightTitle}>
              Sesi monitoring · kasus #{selected.id}{' '}
              <Text style={{ color: STATUS_COLOR[selected.status] }}>({selected.status})</Text>
            </Text>
            <Text style={styles.memberEmail}>
              Ditangani {selected.koas_name} · Pembimbing {selected.supervisor_name}
            </Text>

            {appts.length === 0 && <Text style={styles.empty}>Belum ada sesi.</Text>}
            {appts.map((a) => (
              <View key={a.id} style={styles.member}>
                <View style={styles.memberCopy}>
                  <Text style={styles.memberName}>{fmt(a.scheduled_at)}</Text>
                  <Text style={styles.memberEmail}>{a.notes || '—'}</Text>
                </View>
                <Text style={[styles.statusText, { color: STATUS_COLOR[a.status] }]}>{a.status}</Text>
              </View>
            ))}

            {selected.status !== 'selesai' && (
              <View style={styles.rowGap}>
                <TextInput
                  style={styles.input}
                  placeholder="Jadwal lanjutan, mis. 2026-08-17T09:00"
                  placeholderTextColor={colors.inkMuted}
                  value={apptWhen}
                  onChangeText={setApptWhen}
                  autoCapitalize="none"
                />
                <Pressable
                  style={({ pressed }) => [styles.primaryBtn, pressed && styles.btnPressed]}
                  onPress={onSubmitAppt}
                  disabled={saving}
                >
                  {saving ? (
                    <ActivityIndicator color={colors.ink950} size="small" />
                  ) : (
                    <Text style={styles.primaryBtnText}>Janji temu lanjutan</Text>
                  )}
                </Pressable>
              </View>
            )}
          </View>
        )}
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
  statusText: {
    color: colors.aqua400,
    fontSize: 12,
    fontWeight: '600',
    textTransform: 'uppercase',
  },
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
  chips: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
    marginBottom: 12,
  },
  chip: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 999,
    backgroundColor: colors.white,
    borderWidth: 1,
    borderColor: 'rgba(11, 27, 40, 0.12)',
  },
  chipActive: {
    backgroundColor: colors.aqua500,
    borderColor: colors.aqua500,
  },
  chipText: {
    color: colors.ink900,
    fontSize: 13,
    fontWeight: '600',
  },
  chipTextActive: {
    color: colors.ink950,
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
  empty: {
    color: colors.inkMuted,
    fontSize: 14,
    lineHeight: 20,
  },
  hint: {
    color: colors.inkMuted,
    fontSize: 13,
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
  rowGap: {
    gap: 4,
  },
})
