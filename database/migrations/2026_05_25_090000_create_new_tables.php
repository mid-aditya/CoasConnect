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
        // Assignments table (patient-coas-doctor relationship)
        Schema::create("assignments", function (Blueprint $table) {
            $table->id();
            $table->foreignId("patient_id")->constrained()->cascadeOnDelete();
            $table
                ->foreignId("coas_id")
                ->constrained("users")
                ->cascadeOnDelete();
            $table
                ->foreignId("doctor_id")
                ->constrained("users")
                ->cascadeOnDelete();
            $table->foreignId("rotation_id")->constrained()->cascadeOnDelete();
            $table
                ->enum("status", [
                    "pending",
                    "active",
                    "completed",
                    "transferred",
                    "terminated",
                ])
                ->default("pending");
            $table->timestamp("assigned_at")->nullable();
            $table->timestamp("completed_at")->nullable();
            $table->text("notes")->nullable();
            $table->timestamps();

            $table->index(["coas_id", "status"]);
            $table->index(["doctor_id", "status"]);
            $table->index(["patient_id", "status"]);
        });

        // Competencies table (curriculum tree)
        Schema::create("competencies", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code")->unique();
            $table->text("description")->nullable();
            $table
                ->foreignId("parent_id")
                ->nullable()
                ->constrained("competencies")
                ->nullOnDelete();
            $table->integer("level")->default(0);
            $table->integer("target_count")->default(3);
            $table->boolean("is_active")->default(true);
            $table->timestamps();

            $table->index(["parent_id", "is_active"]);
            $table->index(["level", "is_active"]);
        });

        // Clinical logs table (enhanced)
        Schema::create("clinical_logs", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("assignment_id")
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId("rotation_id")->constrained()->cascadeOnDelete();
            $table
                ->foreignId("user_id")
                ->constrained("users")
                ->cascadeOnDelete();
            $table->timestamp("activity_date");
            $table->enum("activity_type", [
                "anamnesis",
                "physical_exam",
                "procedure",
                "education",
                "consultation",
                "other",
            ]);
            $table->text("description");
            $table
                ->enum("patient_condition", [
                    "stable",
                    "improving",
                    "worsening",
                    "critical",
                ])
                ->default("stable");
            $table->text("reflection")->nullable();
            $table
                ->enum("status", [
                    "draft",
                    "submitted",
                    "reviewed",
                    "revision_requested",
                    "rejected",
                ])
                ->default("draft");
            $table->timestamp("submitted_at")->nullable();
            $table->timestamps();

            $table->index(["user_id", "status"]);
            $table->index(["assignment_id", "activity_date"]);
            $table->index(["status", "submitted_at"]);
        });

        // Clinical log competencies pivot table
        Schema::create("clinical_log_competency", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("clinical_log_id")
                ->constrained()
                ->cascadeOnDelete();
            $table
                ->foreignId("competency_id")
                ->constrained()
                ->cascadeOnDelete();
            $table->tinyInteger("rating")->nullable();
            $table->text("feedback")->nullable();
            $table->timestamps();

            $table->unique(["clinical_log_id", "competency_id"]);
        });

        // Clinical log attachments table
        Schema::create("clinical_log_attachments", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("clinical_log_id")
                ->constrained()
                ->cascadeOnDelete();
            $table->string("filename");
            $table->string("original_filename");
            $table->string("mime_type");
            $table->integer("size");
            $table->string("path");
            $table->timestamps();

            $table->index("clinical_log_id");
        });

        // Evaluations table
        Schema::create("evaluations", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("clinical_log_id")
                ->constrained()
                ->cascadeOnDelete();
            $table
                ->foreignId("evaluator_id")
                ->constrained("users")
                ->cascadeOnDelete();
            $table
                ->enum("status", ["approved", "revision_requested", "rejected"])
                ->default("approved");
            $table->text("feedback")->nullable();
            $table->json("ratings")->nullable();
            $table->timestamp("evaluated_at")->nullable();
            $table->timestamps();

            $table->index(["evaluator_id", "status"]);
            $table->index("clinical_log_id");
        });

        // WhatsApp messages table
        Schema::create("whatsapp_messages", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("assignment_id")
                ->constrained()
                ->cascadeOnDelete();
            $table->enum("direction", ["inbound", "outbound"]);
            $table->text("body"); // encrypted
            $table->boolean("is_sensitive")->default(false);
            $table->boolean("is_emergency")->default(false);
            $table->timestamp("received_at");
            $table->timestamp("processed_at")->nullable();
            $table->json("metadata")->nullable();
            $table->timestamps();

            $table->index(["assignment_id", "direction"]);
            $table->index(["is_emergency", "processed_at"]);
        });

        // WhatsApp templates table
        Schema::create("whats_app_templates", function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->string("code")->unique();
            $table->text("content");
            $table
                ->enum("type", [
                    "welcome",
                    "reminder",
                    "education",
                    "emergency",
                    "custom",
                ])
                ->default("custom");
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });

        // User profiles table (for extended user info)
        Schema::create("user_profiles", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->string("employee_id")->nullable(); // NIP for doctors, NIM for COAS
            $table->string("faculty")->nullable();
            $table->integer("batch")->nullable();
            $table->string("specialization")->nullable();
            $table->string("department")->nullable();
            $table->integer("supervision_quota")->default(5);
            $table->string("document_path")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("user_profiles");
        Schema::dropIfExists("whats_app_templates");
        Schema::dropIfExists("whatsapp_messages");
        Schema::dropIfExists("evaluations");
        Schema::dropIfExists("clinical_log_attachments");
        Schema::dropIfExists("clinical_log_competency");
        Schema::dropIfExists("clinical_logs");
        Schema::dropIfExists("competencies");
        Schema::dropIfExists("assignments");
    }
};
