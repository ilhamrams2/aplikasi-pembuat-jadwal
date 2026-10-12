<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Teachers
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->integer('order_no')->nullable(); // No Urut (1..37)
            $table->string('nip')->nullable();
            $table->string('name');
            $table->string('additional_role')->nullable(); // Kepala Madrasah, Waka, etc.
            $table->integer('target_hours')->default(24); // Beban jam total (e.g. 28, 36)
            $table->string('phone')->nullable();
            $table->string('color', 20)->default('#2563EB');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Classrooms (Rombel)
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // X.1, X.2, etc.
            $table->integer('grade'); // 10, 11, 12
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Subjects
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->default('Wajib'); // Wajib, Tingkat Lanjut, Muatan Lokal, dll.
            $table->string('color', 20)->default('#3B82F6');
            $table->timestamps();
        });

        // 4. Teacher Assignments (Distribusi Pembagian Tugas Guru ke Kelas)
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->integer('hours_per_week')->default(2); // JP per minggu (misal 2, 3, 5)
            $table->timestamps();
        });

        // 5. Time Slots (Jam Pelajaran)
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('day'); // Senin, Selasa, Rabu, Kamis, Jumat
            $table->integer('period'); // 1..10
            $table->string('start_time', 10); // e.g. 06:45
            $table->string('end_time', 10); // e.g. 07:30
            $table->boolean('is_break')->default(false);
            $table->string('break_name')->nullable(); // Upacara, Istirahat 1, Sholat Dzuhur
            $table->timestamps();
        });

        // 6. Schedules (Jadwal Terjadwal)
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('time_slots')->cascadeOnDelete();
            $table->foreignId('teacher_assignment_id')->nullable()->constrained('teacher_assignments')->nullOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('day');
            $table->integer('period');
            $table->string('room')->nullable();
            $table->timestamps();

            // Constraint: Satu kelas tidak boleh memiliki lebih dari 1 mapel di jam yang sama
            $table->unique(['classroom_id', 'time_slot_id']);
        });

        // 7. School Settings
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('school_settings');
    }
};
