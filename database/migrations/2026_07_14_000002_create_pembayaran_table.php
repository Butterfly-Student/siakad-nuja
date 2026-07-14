<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('nominal', 12, 2);
            $table->string('metode', 30)->default('Transfer'); // Transfer, Tunai
            $table->string('bank', 50)->nullable();
            $table->string('nama_pengirim', 150)->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('bukti', 255)->nullable(); // path lampiran bukti transfer
            $table->string('status', 20)->default('menunggu'); // menunggu, disetujui, ditolak
            $table->text('catatan')->nullable();          // catatan pengirim
            $table->text('alasan_tolak')->nullable();      // alasan admin menolak
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();

            $table->index(['tagihan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
