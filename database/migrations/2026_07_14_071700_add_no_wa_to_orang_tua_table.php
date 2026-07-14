<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orang_tua', function (Blueprint $table) {
            // Nomor WhatsApp terpisah dari nomor HP umum
            $table->string('no_wa', 20)->nullable()->unique()->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('orang_tua', function (Blueprint $table) {
            $table->dropUnique(['no_wa']);
            $table->dropColumn('no_wa');
        });
    }
};
