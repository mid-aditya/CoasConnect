<?php

namespace Database\Seeders;

use App\Models\WhatsAppTemplate;
use Illuminate\Database\Seeder;

class WhatsAppTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Message',
                'code' => 'welcome',
                'content' => "Selamat datang di CoasConnect! 👋

Silakan ketik NIK atau nomor registrasi RS Anda untuk verifikasi.

Jika butuh bantuan, ketik 'BANTUAN'.",
                'type' => 'welcome',
            ],
            [
                'name' => 'Welcome Assigned',
                'code' => 'welcome_assigned',
                'content' => "Halo {{patient_name}}! Anda terhubung dengan COAS {{coas_name}}.

Kirimkan pesan kapan saja untuk konsultasi kesehatan Anda.

Pesan ini adalahpercakapan dengan COAS, bukan pengganti konsultasi dokter.",
                'type' => 'welcome',
            ],
            [
                'name' => 'Reminder Medication',
                'code' => 'reminder_medication',
                'content' => "Pengingat Obat 🕐

Hai {{patient_name}}, ini pengingat untuk minum obat:

{{medication}} - {{dosage}}

Jangan lupa minum obat sesuai jadwal ya!",
                'type' => 'reminder',
            ],
            [
                'name' => 'Reminder Appointment',
                'code' => 'reminder_appointment',
                'content' => "Pengingat Jadwal Kontrol 📅

Hai {{patient_name}},

Anda memiliki jadwal kontrol pada:
📆 Tanggal: {{date}}
🕐 Waktu: {{time}}

Jika berhalangan hadir, silakan hubungi kami.",
                'type' => 'reminder',
            ],
            [
                'name' => 'Emergency Acknowledgment',
                'code' => 'emergency_alert',
                'content' => "⚠️ PERHATIAN

Pesan Anda mengandung kata kunci darurat.
Tim medis akan segera meninjau kondisi Anda.

Mohon tetap tenang dan siap menerima panggilan dari tim kesehatan kami.

Jika kondisi sangat mendesak, segera hubungi IGD: [Nomor IGD]",
                'type' => 'emergency',
            ],
            [
                'name' => 'Symptom Received',
                'code' => 'symptom_received',
                'content' => "Terima kasih {{patient_name}}! 🙏

Gejala Anda telah dicatat:
{{symptoms}}

COAS {{coas_name}} akan meninjaunya shortly.

Jika kondisi memburuk, segera hubungi kami atau datang ke IGD.",
                'type' => 'custom',
            ],
            [
                'name' => 'Health Education',
                'code' => 'education_diabetes',
                'content' => "📚 Edukasi Kesehatan: Diabetes

Hai {{patient_name}},

Diabetes adalah penyakit kronis yang membutuhkan pengelolaan sehari-hari:

🍽️ Pola Makan:
- Makan teratur 3x sehari
- Kurangi gula dan karbohidrat sederhana
- Perbanyak sayur dan serat

🏃 Aktivitas Fisik:
- Olahraga ringan 30 menit/hari
- Jalan kaki, bersepeda, atau berenang

💊 Pengobatan:
- Minum obat teratur sesuai anjuran dokter
- Jangan stop obat sendiri

🩺 Kontrol Rutin:
- Periksa gula darah secara rutin
- Kunjungi dokter sesuai jadwal

Ada pertanyaan? Kirimkan pesan ke kami!",
                'type' => 'education',
            ],
            [
                'name' => 'No Active Assignment',
                'code' => 'no_active_coas',
                'content' => "Halo {{patient_name}},

Terima kasih telah menghubungi kami. Saat ini belum ada COAS yang ditugaskan untuk Anda.

Tim kami akan segera menugaskan COAS. Mohon bersabar ya!

Anda akan menerima pesan konfirmasi setelah COAS ditugaskan.",
                'type' => 'custom',
            ],
        ];

        foreach ($templates as $template) {
            WhatsAppTemplate::create($template);
        }
    }
}
