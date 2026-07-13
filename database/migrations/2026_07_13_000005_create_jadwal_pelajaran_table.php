<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('hari', 15);
            $table->unsignedTinyInteger('jam_ke');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan', 50)->nullable();
            $table->string('tahun_ajaran', 15);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }
};
