<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas', 50);
            $table->string('tingkat', 10);
            $table->string('jenjang', 20);
            $table->string('tahun_ajaran', 15);
            $table->foreignId('wali_kelas_id')->nullable()->constrained('guru')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedTinyInteger('kapasitas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
