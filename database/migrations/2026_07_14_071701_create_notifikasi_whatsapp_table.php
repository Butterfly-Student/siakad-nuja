<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_whatsapp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orang_tua_id')->nullable()->constrained('orang_tua')->nullOnDelete();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->nullOnDelete();
            $table->string('no_tujuan', 20);
            $table->enum('jenis', ['absensi', 'nilai', 'pengumuman', 'tagihan', 'kuitansi', 'umum']);
            $table->text('isi_pesan');
            $table->enum('status', ['pending', 'terkirim', 'gagal'])->default('pending');
            $table->timestamp('dikirim_pada')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('orang_tua_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_whatsapp');
    }
};
