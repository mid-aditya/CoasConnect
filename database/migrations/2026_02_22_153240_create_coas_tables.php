<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Administrator Fakultas, Koordinator Program, Dosen Pembimbing, Koas
            $table->timestamps();
        });

        // Add role to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
        });

        Schema::create('academic_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Angkatan
            $table->integer('year');
            $table->timestamps();
        });

        Schema::create('rotations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Penyakit Dalam, Bedah, Anak, dll
            $table->integer('min_procedures')->default(0); // target kompetensi minimal
            $table->timestamps();
        });

        Schema::create('rotation_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Koas
            $table->foreignId('rotation_id')->constrained('rotations')->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained('academic_periods')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete(); // Dosen
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Koas
            $table->string('initials'); // A.B.
            $table->string('age_category'); // Anak, Dewasa, Lansia dll
            $table->enum('gender', ['L', 'P']);
            $table->string('working_diagnosis');
            $table->string('care_context'); // IGD, Rawat Inap, Poli
            $table->timestamps();
        });

        Schema::create('patient_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Koas
            $table->foreignId('rotation_id')->constrained('rotations')->cascadeOnDelete();
            $table->date('log_date');
            $table->text('condition_summary');
            $table->text('key_examination');
            $table->text('treatment_plan');
            $table->text('procedures_performed')->nullable();
            $table->text('clinical_reflection');
            $table->enum('status', ['draft', 'submitted', 'revised', 'approved'])->default('draft');
            $table->text('supervisor_comment')->nullable();
            $table->timestamps();
        });

        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rotation_id')->constrained('rotations')->cascadeOnDelete();
            $table->string('name'); // Nama tindakan/kompetensi
            $table->string('competency_level')->nullable();
            $table->timestamps();
        });

        Schema::create('procedure_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_log_id')->nullable()->constrained('patient_logs')->nullOnDelete();
            $table->foreignId('procedure_id')->constrained('procedures')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Koas
            $table->enum('involvement_level', ['observasi', 'asistensi', 'mandiri']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete(); // Dosen
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action'); // "status_changed_to_approved", "log_updated"
            $table->string('model_type'); // "App\Models\PatientLog"
            $table->unsignedBigInteger('model_id'); // 1
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('procedure_validations');
        Schema::dropIfExists('procedures');
        Schema::dropIfExists('patient_logs');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('rotation_assignments');
        Schema::dropIfExists('rotations');
        Schema::dropIfExists('cohorts');
        Schema::dropIfExists('academic_periods');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
