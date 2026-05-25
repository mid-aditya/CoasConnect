<?php

namespace Database\Seeders;

use App\Models\Competency;
use Illuminate\Database\Seeder;

class CompetencyTreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SKDI-based curriculum structure (Standar Kompetensi Dokter Indonesia)

        // Domain 1: Profesionalitas
        $profesional = Competency::create([
            'name' => 'Profesionalitas',
            'code' => 'PROF',
            'description' => 'Menunjukkan perilaku profesional dalam praktik kedokteran',
            'parent_id' => null,
            'level' => 0,
            'target_count' => 5,
            'is_active' => true,
        ]);

        $this->createChildren($profesional, 1, [
            ['name' => 'Etika Kedokteran', 'code' => 'PROF-01', 'target' => 3],
            ['name' => 'Konseling & Komunikasi', 'code' => 'PROF-02', 'target' => 3],
            ['name' => 'Manajemen Diri', 'code' => 'PROF-03', 'target' => 2],
        ]);

        // Domain 2: Pengetahuan
        $pengetahuan = Competency::create([
            'name' => 'Pengetahuan',
            'code' => 'PENGET',
            'description' => 'Menerapkan pengetahuan biomedik, klinis, dan perilaku dalam praktik kedokteran',
            'parent_id' => null,
            'level' => 0,
            'target_count' => 8,
            'is_active' => true,
        ]);

        $this->createChildren($pengetahuan, 1, [
            ['name' => 'Ilmu Biomedik Dasar', 'code' => 'PENGET-01', 'target' => 3],
            ['name' => 'Ilmu Klinis', 'code' => 'PENGET-02', 'target' => 5],
            ['name' => 'Ilmu Kesehatan Masyarakat', 'code' => 'PENGET-03', 'target' => 2],
        ]);

        // Domain 3: Keterampilan Klinis
        $keterampilan = Competency::create([
            'name' => 'Keterampilan Klinis',
            'code' => 'KET',
            'description' => 'Melakukan keterampilan klinis dasar',
            'parent_id' => null,
            'level' => 0,
            'target_count' => 10,
            'is_active' => true,
        ]);

        $anamnesis = Competency::create([
            'name' => 'Anamnesis & Pemeriksaan Fisik',
            'code' => 'KET-01',
            'description' => 'Kemampuan melakukan anamnesis dan pemeriksaan fisik',
            'parent_id' => $keterampilan->id,
            'level' => 1,
            'target_count' => 5,
            'is_active' => true,
        ]);

        $this->createChildren($anamnesis, 2, [
            ['name' => 'Anamnesis Umum', 'code' => 'KET-01-01', 'target' => 3],
            ['name' => 'Anamnesis Spesifik Sistem', 'code' => 'KET-01-02', 'target' => 3],
            ['name' => 'Pemeriksaan Fisik Umum', 'code' => 'KET-01-03', 'target' => 3],
            ['name' => 'Pemeriksaan Fisik Spesifik', 'code' => 'KET-01-04', 'target' => 3],
        ]);

        $diagnostik = Competency::create([
            'name' => 'Pemeriksaan Penunjang',
            'code' => 'KET-02',
            'description' => 'Kemampuan menginterpretasi pemeriksaan penunjang',
            'parent_id' => $keterampilan->id,
            'level' => 1,
            'target_count' => 4,
            'is_active' => true,
        ]);

        $this->createChildren($diagnostik, 2, [
            ['name' => 'Laboratorium Klinik', 'code' => 'KET-02-01', 'target' => 3],
            ['name' => 'Radiologi Dasar', 'code' => 'KET-02-02', 'target' => 2],
            ['name' => 'EKG', 'code' => 'KET-02-03', 'target' => 2],
        ]);

        $tindakan = Competency::create([
            'name' => 'Tindakan Dasar',
            'code' => 'KET-03',
            'description' => 'Kemampuan melakukan tindakan medis dasar',
            'parent_id' => $keterampilan->id,
            'level' => 1,
            'target_count' => 5,
            'is_active' => true,
        ]);

        $this->createChildren($tindakan, 2, [
            ['name' => 'Injeksi (IV, IM, SC)', 'code' => 'KET-03-01', 'target' => 3],
            ['name' => 'Pemasangan Infus', 'code' => 'KET-03-02', 'target' => 3],
            ['name' => 'Jahitan Luka', 'code' => 'KET-03-03', 'target' => 2],
            ['name' => 'Kateterisasi', 'code' => 'KET-03-04', 'target' => 2],
            ['name' => 'WSD/Chest Tube', 'code' => 'KET-03-05', 'target' => 1],
        ]);

        // Domain 4: Pengelolaan Masalah Kesehatan
        $pengelolaan = Competency::create([
            'name' => 'Pengelolaan Masalah Kesehatan',
            'code' => 'PENG',
            'description' => 'Kemampuan mengelola masalah kesehatan secara holistik',
            'parent_id' => null,
            'level' => 0,
            'target_count' => 6,
            'is_active' => true,
        ]);

        $this->createChildren($pengelolaan, 1, [
            ['name' => 'Diagnosis Banding', 'code' => 'PENG-01', 'target' => 3],
            ['name' => 'Perencanaan Terapi', 'code' => 'PENG-02', 'target' => 3],
            ['name' => 'Pemberian Obat & Resep', 'code' => 'PENG-03', 'target' => 2],
            ['name' => 'Manajemen Lanjutan', 'code' => 'PENG-04', 'target' => 2],
        ]);

        // Domain 5: Komunikasi & Edukasi
        $komunikasi = Competency::create([
            'name' => 'Komunikasi & Edukasi',
            'code' => 'KOM',
            'description' => 'Kemampuan berkomunikasi dan edukasi kesehatan',
            'parent_id' => null,
            'level' => 0,
            'target_count' => 4,
            'is_active' => true,
        ]);

        $this->createChildren($komunikasi, 1, [
            ['name' => 'Komunikasi dengan Pasien', 'code' => 'KOM-01', 'target' => 3],
            ['name' => 'Edukasi Kesehatan', 'code' => 'KOM-02', 'target' => 2],
            ['name' => 'Dokumentasi Medis', 'code' => 'KOM-03', 'target' => 2],
        ]);
    }

    /**
     * Create child competencies.
     */
    private function createChildren(Competency $parent, int $level, array $children): void
    {
        foreach ($children as $child) {
            Competency::create([
                'name' => $child['name'],
                'code' => $child['code'],
                'description' => null,
                'parent_id' => $parent->id,
                'level' => $level,
                'target_count' => $child['target'],
                'is_active' => true,
            ]);
        }
    }
}
