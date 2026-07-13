<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwal_pelajaran')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal');
            $table->string('status', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
