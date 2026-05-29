<?php

namespace Database\Seeders;

use App\Models\Rotation;
use App\Models\Procedure;
use Illuminate\Database\Seeder;

class RotationSeeder extends Seeder
{
    public function run(): void
    {
        $rotations = [
            [
                'name' => 'Ilmu Penyakit Dalam (IPD)',
                'min_procedures' => 20,
                'procedures' => [
                    'Anamnesis Pasien DM',
                    'Anamnesis Pasien Hipertensi',
                    'Pemeriksaan Fisik Abdomen',
                    'Pemeriksaan Fisik Jantung',
                    'Insersi Infus',
                    'Pungsi Arteri',
                    'Reading EKG',
                    'Interpretasi Rontgen Thorax',
                    'Asesmen Gula Darah',
                    'Manajemen Luka Diabetes',
                ],
            ],
            [
                'name' => 'Ilmu Kesehatan Anak (IKA)',
                'min_procedures' => 15,
                'procedures' => [
                    'Anamnesis Anak Sehat',
                    'Anamnesis Anak Sakit',
                    'Pemeriksaan Tumbuh Kembang',
                    'Imunisasi',
                    'Pemberian Obat Anak',
                    'Nebulizer',
                    'Pemeriksaan THT Anak',
                    'Reading Foto Thorax Anak',
                    'Manajemen Dehidrasi',
                    'Konseling Gizi Anak',
                ],
            ],
            [
                'name' => 'Ilmu Bedah',
                'min_procedures' => 18,
                'procedures' => [
                    'Anamnesis Pasien Bedah',
                    'Pemeriksaan Fisik Abdomen Akut',
                    'Penjahitan Luka',
                    'Dressing Luka',
                    'Insersi Kateter',
                    'Wound Care',
                    'Biopsy Kulit',
                    'Appendektomi Assist',
                    'Herniorrhaphy Assist',
                    'Cholecystectomy Assist',
                ],
            ],
            [
                'name' => 'Ilmu Kebidanan & Kandungan (Obsgyn)',
                'min_procedures' => 15,
                'procedures' => [
                    'Anamnesis Ibu Hamil',
                    'Pemeriksaan IVA',
                    'Insersi IUD',
                    'Pemeriksaan CTG',
                    'Persalinan Normal Assist',
                    'Episiotomy & Repair',
                    'Konseling KB',
                    'Pamflet Antenatal',
                    'Preeklampsia Management',
                    'Postpartum Care',
                ],
            ],
            [
                'name' => 'Ilmu Penyakit Saraf (Neurologi)',
                'min_procedures' => 10,
                'procedures' => [
                    'Pemeriksaan Status Neurologis',
                    'Anamnesis Stroke',
                    'Anamnesis Epilepsi',
                    'Pemeriksaan Mingografi',
                    'Reading CT Scan Otak',
                    'Manajemen Stroke Akut',
                    'Neuropati Perifer',
                    'Pemeriksaan Likuor',
                ],
            ],
            [
                'name' => 'Ilmu Kesehatan Jiwa (Psikiatri)',
                'min_procedures' => 10,
                'procedures' => [
                    'Anamnesis Psikiatri',
                    'Pemeriksaan Status Mental',
                    'Skrining Depresi (BDI)',
                    'Skrining Kecemasan (GAD-7)',
                    'Skrining Demensia',
                    'Mini Mental State Examination',
                    'Psikoedukasi Keluarga',
                    'Konseling Terapi',
                    'Manajemen Bunuh Diri',
                    'Pemberian Obat Psikotropika',
                ],
            ],
            [
                'name' => 'Ilmu Mata',
                'min_procedures' => 10,
                'procedures' => [
                    'Pemeriksaan Visus',
                    'Pemeriksaan Lapang Pandang',
                    'Tonometri',
                    'Slit Lamp Examination',
                    'Funduskopi',
                    'Pemeriksaan Kelopak Mata',
                    'Flush Attachment',
                    'Irissage',
                    'Toping',
                    'Sentidosis',
                ],
            ],
            [
                'name' => 'Ilmu THT',
                'min_procedures' => 10,
                'procedures' => [
                    'Pemeriksaan Telinga Mikroskopik',
                    'Pemeriksaan Pendengaran',
                    'Nasofaringoscopy',
                    'Laringoscopy',
                    'Insersi Ear Wick',
                    'Cecostomy Care',
                    'Post Op Tonsilektomi',
                    'Trakeostomy Care',
                    'BDS/SPIR',
                    'Audioometri',
                ],
            ],
            [
                'name' => 'Ilmu Kulit & Kelamin (Dermatovenereologi)',
                'min_procedures' => 10,
                'procedures' => [
                    'Pemeriksaan Dermatologi',
                    'KOH Preparation',
                    'Wood Lamp Examination',
                    'Patch Test',
                    'Skin Biopsy',
                    'Krioterapi',
                    'Elektrokauter',
                    'Pemberian Obat Topikal',
                    'Konseling PASI',
                    'Dermatologi Kosmetik',
                ],
            ],
            [
                'name' => 'Radiologi',
                'min_procedures' => 8,
                'procedures' => [
                    'Reading Foto Thorax PA',
                    'Reading Foto Abdomen',
                    'Reading CT Scan',
                    'Reading MRI',
                    'Reading USG',
                    'Pemeriksaan Mammografi',
                    'Fluoroscopy',
                    'Intervensi Radiologi',
                ],
            ],
            [
                'name' => 'Anestesiologi & Reanimasi',
                'min_procedures' => 10,
                'procedures' => [
                    'Airway Management',
                    'Intubasi Endotrakeal',
                    'Ventilasi Masker',
                    'Intubasi SAD',
                    'Regional Anestesi',
                    'Spinal Anestesi',
                    'Epidural Anestesi',
                    'Local Anestesi Infiltrasi',
                    'Monitoring Vital Sign',
                    'Resusitasi Jantung Paru',
                ],
            ],
            [
                'name' => 'Ilmu Kedokteran Forensik & Medikolegal',
                'min_procedures' => 8,
                'procedures' => [
                    'Pemeriksaan Visum',
                    'Pemeriksaan Jenazah',
                    'Surat Keterangan Medik',
                    'Surat Keterangan Kematian',
                    'Pemeriksaan KHP',
                    'Dokumentasi Luka',
                    'Rekonstruksi Kejadian',
                    'Konsultasi Forensik',
                ],
            ],
        ];

        foreach ($rotations as $rotationData) {
            $rotation = Rotation::create([
                'name' => $rotationData['name'],
                'min_procedures' => $rotationData['min_procedures'],
            ]);

            foreach ($rotationData['procedures'] as $index => $procedureName) {
                Procedure::create([
                    'rotation_id' => $rotation->id,
                    'name' => $procedureName,
                    'competency_level' => $index < 3 ? 'basic' : ($index < 7 ? 'intermediate' : 'advanced'),
                ]);
            }
        }
    }
}
