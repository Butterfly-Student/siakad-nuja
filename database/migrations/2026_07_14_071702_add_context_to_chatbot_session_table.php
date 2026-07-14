<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_session', function (Blueprint $table) {
            $table->foreignId('orang_tua_id')->nullable()->constrained('orang_tua')->nullOnDelete()->after('no_hp');
            $table->foreignId('anak_terpilih_id')->nullable()->constrained('siswa')->nullOnDelete()->after('orang_tua_id');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_session', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anak_terpilih_id');
            $table->dropConstrainedForeignId('orang_tua_id');
        });
    }
};
