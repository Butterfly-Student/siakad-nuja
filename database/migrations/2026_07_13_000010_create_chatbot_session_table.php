<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_session', function (Blueprint $table) {
            $table->id();
            $table->string('no_hp', 20)->unique();
            $table->string('state', 50)->nullable();
            $table->json('data_context')->nullable();
            $table->timestamp('last_activity')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_session');
    }
};
