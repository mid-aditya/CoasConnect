import { useCallback, useEffect, useState } from 'react'
import {
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Linking,
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
  createCampaign,
  getCampaigns,
  getHealth,
  getMe,
  getMyCampaigns,
  getToken,
  setToken,
  updateCampaign,
  type CampaignView,
  type Health,
  type User,
} from './src/api/client'
import { colors } from './src/theme'
import AuthScreen from './src/AuthScreen'

const STATUS_COLOR: Record<string, string> = {
  aktif: colors.aqua400,
  tutup: colors.coral400,
}

const fmtDate = (iso: string) =>
  new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })

const EMPTY_FORM = { title: '', description: '', criteria: '', procedure: '', whatsapp: '' }

export default function App() {
  const [authed, setAuthed] = useState(() => getToken() !== null)
  const [me, setMe] = useState<User | null>(null)
  const [health, setHealth] = useState<Health | null>(null)
  const [campaigns, setCampaigns] = useState<CampaignView[]>([])
  const [selected, setSelected] = useState<CampaignView | null>(null)
  const [form, setForm] = useState(EMPTY_FORM)
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [refreshing, setRefreshing] = useState(false)

  const load = useCallback(async () => {
    try {
      const h = await getHealth()
      setHealth(h)
    } catch {
      setHealth(null)
    }
    try {
      const c = me?.role === 'koas' ? await getMyCampaigns() : await getCampaigns()
      setCampaigns(c.data)
    } catch {
      setCampaigns([])
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [me?.role])

  useEffect(() => {
    if (!authed) return
    getMe()
      .then((r) => setMe(r.data))
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

  const onWhatsApp = (number: string) => {
    Linking.openURL(`https://wa.me/${number}`).catch(() =>
      Alert.alert('Gagal membuka WhatsApp', 'Periksa koneksi atau nomor tujuan.'),
    )
  }

  const onCreate = async () => {
    if (!form.title.trim() || !form.criteria.trim() || !form.whatsapp.trim()) {
      Alert.alert('Form belum lengkap', 'Judul, kriteria, dan nomor WhatsApp wajib diisi.')
      return
    }
    setSaving(true)
    try {
      const res = await createCampaign({
        title: form.title.trim(),
        description: form.description.trim(),
        criteria: form.criteria.trim(),
        procedure: form.procedure.trim(),
        whatsapp: form.whatsapp.trim(),
      })
      setForm(EMPTY_FORM)
      setCampaigns((list) => [res.data, ...list])
      Alert.alert('Kampanye terpasang', `"${res.data.title}" aktif dan bisa ditemukan pasien.`)
    } catch (e) {
      Alert.alert('Gagal memasang kampanye', e instanceof Error ? e.message : 'Terjadi kesalahan.')
    } finally {
      setSaving(false)
    }
  }

  const onToggle = async (c: CampaignView) => {
    try {
      const res = await updateCampaign(c.id, { status: c.status === 'aktif' ? 'tutup' : 'aktif' })
      setCampaigns((list) => list.map((x) => (x.id === c.id ? res.data : x)))
      setSelected((s) => (s?.id === c.id ? res.data : s))
    } catch (e) {
      Alert.alert('Gagal mengubah status', e instanceof Error ? e.message : 'Terjadi kesalahan.')
    }
  }

  if (!authed) {
    return <AuthScreen onAuthed={() => setAuthed(true)} />
  }

  const isKoas = me?.role === 'koas'
  const isSpesialis = me?.role === 'spesialis'

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
              {me ? `${me.name} · ${me.role}` : 'Penjaringan pasien · dokter koas'}
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

        {/* Koas: profil + pasang kampanye */}
        {isKoas && (
          <>
            <View style={[styles.card, styles.darkCard]}>
              <Text style={styles.cardLabel}>Profil koas</Text>
              <View style={styles.profileRows}>
                <View style={styles.cardRow}>
                  <Text style={styles.monoLabel}>RS</Text>
                  <Text style={styles.cardDetailStrong}>{me?.hospital || '—'}</Text>
                </View>
                <View style={styles.cardRow}>
                  <Text style={styles.monoLabel}>Bidang</Text>
                  <Text style={styles.cardDetailStrong}>{me?.specialty || '—'}</Text>
                </View>
              </View>
              <Text style={styles.cardDetail}>
                Pembimbing (dokter spesialis) akan dihubungkan lewat fitur profiling koas.
              </Text>
            </View>

            <View style={[styles.card, styles.lightCard]}>
              <Text style={styles.lightTitle}>Pasang kampanye baru</Text>
              <TextInput
                style={styles.input}
                placeholder="Judul (cth: Program Pendampingan Hipertensi)"
                placeholderTextColor={colors.inkMuted}
                value={form.title}
                onChangeText={(t) => setForm((f) => ({ ...f, title: t }))}
              />
              <TextInput
                style={styles.input}
                placeholder="Deskripsi singkat"
                placeholderTextColor={colors.inkMuted}
                value={form.description}
                onChangeText={(t) => setForm((f) => ({ ...f, description: t }))}
                multiline
              />
              <TextInput
                style={styles.input}
                placeholder="Kriteria pasien (cth: hipertensi usia 40–65 th)"
                placeholderTextColor={colors.inkMuted}
                value={form.criteria}
                onChangeText={(t) => setForm((f) => ({ ...f, criteria: t }))}
                multiline
              />
              <TextInput
                style={styles.input}
                placeholder={'Prosedur pendaftaran (1) … 2) …'}
                placeholderTextColor={colors.inkMuted}
                value={form.procedure}
                onChangeText={(t) => setForm((f) => ({ ...f, procedure: t }))}
                multiline
              />
              <TextInput
                style={styles.input}
                placeholder="Nomor WhatsApp (format: 628…)"
                placeholderTextColor={colors.inkMuted}
                value={form.whatsapp}
                onChangeText={(t) => setForm((f) => ({ ...f, whatsapp: t }))}
                keyboardType="phone-pad"
                autoCapitalize="none"
              />
              <Pressable
                style={({ pressed }) => [styles.primaryBtn, pressed && styles.btnPressed]}
                onPress={onCreate}
                disabled={saving}
              >
                {saving ? (
                  <ActivityIndicator color={colors.ink950} size="small" />
                ) : (
                  <Text style={styles.primaryBtnText}>Pasang kampanye</Text>
                )}
              </Pressable>
            </View>
          </>
        )}

        {/* Daftar kampanye */}
        <View style={[styles.card, styles.lightCard]}>
          <Text style={styles.lightTitle}>
            {isKoas ? 'Kampanye saya' : isSpesialis ? 'Kampanye di bawah supervisi' : 'Cari kampanye'}{' '}
            <Text style={styles.count}>{campaigns.length > 0 ? `· ${campaigns.length}` : ''}</Text>
          </Text>

          {!loading && campaigns.length === 0 ? (
            <Text style={styles.empty}>
              {isKoas
                ? 'Belum ada kampanye. Pasang kampanye pertama di atas.'
                : 'Belum ada kampanye yang tersedia saat ini.'}
            </Text>
          ) : (
            campaigns.map((c) => (
              <Pressable key={c.id} onPress={() => setSelected(c)}>
                <View style={styles.member}>
                  <View style={styles.avatar}>
                    <Text style={styles.avatarText}>{c.koas_name.charAt(0).toUpperCase()}</Text>
                  </View>
                  <View style={styles.memberCopy}>
                    <Text style={styles.memberName} numberOfLines={1}>
                      {c.title}
                    </Text>
                    <Text style={styles.memberEmail} numberOfLines={1}>
                      {c.koas_name} · {c.hospital || '—'} · {c.specialty || '—'}
                    </Text>
                    <Text style={styles.memberMeta} numberOfLines={1}>
                      Dibuat {fmtDate(c.created_at)} · {c.whatsapp}
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

        {/* Detail kampanye */}
        {selected && (
          <View style={[styles.card, styles.lightCard]}>
            <View style={styles.cardRow}>
              <Text style={styles.lightTitle}>Detail kampanye</Text>
              <Text style={[styles.statusText, { color: STATUS_COLOR[selected.status] }]}>
                {selected.status}
              </Text>
            </View>
            <Text style={styles.memberName}>{selected.title}</Text>
            {!!selected.description && (
              <Text style={styles.memberEmail}>{selected.description}</Text>
            )}
            <View style={styles.chips}>
              {[selected.specialty, selected.hospital].filter(Boolean).map((t) => (
                <View key={t} style={styles.chip}>
                  <Text style={styles.chipText}>{t}</Text>
                </View>
              ))}
            </View>

            <Text style={styles.detailLabel}>Kriteria pasien</Text>
            <Text style={styles.memberEmail}>{selected.criteria}</Text>

            {!!selected.procedure && (
              <>
                <Text style={styles.detailLabel}>Prosedur</Text>
                <Text style={styles.memberEmail}>{selected.procedure}</Text>
              </>
            )}

            <View style={styles.profileRows}>
              <View style={styles.cardRow}>
                <Text style={styles.monoLabel}>Koas</Text>
                <Text style={styles.memberName}>{selected.koas_name}</Text>
              </View>
              <View style={styles.cardRow}>
                <Text style={styles.monoLabel}>Pembimbing</Text>
                <Text style={styles.memberName}>{selected.supervisor_name || '—'}</Text>
              </View>
            </View>

            {!isSpesialis && (
              <Pressable
                style={({ pressed }) => [styles.primaryBtn, pressed && styles.btnPressed]}
                onPress={() => onWhatsApp(selected.whatsapp)}
              >
                <Text style={styles.primaryBtnText}>Daftar lewat WhatsApp</Text>
              </Pressable>
            )}

            {isKoas && (
              <Pressable
                style={({ pressed }) => [styles.secondaryBtn, pressed && styles.btnPressed]}
                onPress={() => onToggle(selected)}
              >
                <Text style={styles.secondaryBtnText}>
                  {selected.status === 'aktif' ? 'Tutup kampanye' : 'Aktifkan kembali'}
                </Text>
              </Pressable>
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
    backgroundColor: 'rgba(46, 125, 91, 0.15)',
    borderWidth: 1,
    borderColor: 'rgba(46, 125, 91, 0.4)',
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
  cardDetail: {
    color: colors.textDim,
    fontSize: 13,
    marginTop: 10,
  },
  cardDetailStrong: {
    color: colors.sand100,
    fontSize: 14,
    fontWeight: '500',
  },
  monoLabel: {
    color: colors.textFaint,
    fontSize: 12,
    fontWeight: '600',
    textTransform: 'uppercase',
    letterSpacing: 0.6,
  },
  profileRows: {
    gap: 8,
    marginTop: 12,
  },
  statusPill: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 999,
    backgroundColor: 'rgba(46, 125, 91, 0.2)',
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
  lightTitle: {
    color: colors.ink900,
    fontSize: 16,
    fontWeight: '700',
    marginBottom: 14,
    flexShrink: 1,
  },
  count: { color: colors.ocean700 },
  chips: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
    marginTop: 10,
    marginBottom: 14,
  },
  chip: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
    backgroundColor: colors.mist100,
  },
  chipText: {
    color: colors.ocean700,
    fontSize: 12,
    fontWeight: '600',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
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
  secondaryBtn: {
    backgroundColor: colors.mist100,
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 8,
  },
  btnPressed: { opacity: 0.85, transform: [{ scale: 0.99 }] },
  primaryBtnText: {
    color: colors.ink950,
    fontSize: 15,
    fontWeight: '700',
  },
  secondaryBtnText: {
    color: colors.ocean700,
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
    borderTopColor: 'rgba(22, 36, 29, 0.06)',
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
  memberMeta: {
    color: colors.inkMuted,
    fontSize: 11,
    marginTop: 2,
    fontFamily: Platform.select({ ios: 'Menlo', android: 'monospace' }),
  },
  detailLabel: {
    color: colors.ocean700,
    fontSize: 11,
    fontWeight: '700',
    textTransform: 'uppercase',
    letterSpacing: 0.8,
    marginTop: 12,
    marginBottom: 4,
  },
})
