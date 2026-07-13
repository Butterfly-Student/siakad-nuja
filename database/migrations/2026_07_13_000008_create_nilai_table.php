<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('semester', 10);
            $table->string('tahun_ajaran', 15);
            $table->decimal('nilai_harian', 5, 2)->nullable();
            $table->decimal('nilai_uts', 5, 2)->nullable();
            $table->decimal('nilai_uas', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('predikat', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
