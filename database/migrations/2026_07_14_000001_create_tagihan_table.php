<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('judul', 150);
            $table->string('jenis', 30)->default('SPP'); // SPP, Daftar Ulang, Kegiatan, Lainnya
            $table->string('periode', 30)->nullable();    // mis. "Januari 2025" / "2024/2025"
            $table->decimal('nominal', 12, 2);
            $table->date('jatuh_tempo')->nullable();
            $table->string('status', 25)->default('belum_dibayar'); // belum_dibayar, menunggu_verifikasi, lunas
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
