<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_log', function (Blueprint $table) {
            $table->id();
            $table->string('no_hp', 20);
            $table->text('pesan_masuk')->nullable();
            $table->text('pesan_keluar')->nullable();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->cascadeOnUpdate()->nullOnDelete();
            $table->string('intent', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_log');
    }
};
