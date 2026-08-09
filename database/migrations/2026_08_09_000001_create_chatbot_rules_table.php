<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('chatbot_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('keyword', 50)->unique();
            $table->string('judul_menu', 150);
            $table->enum('tipe_action', ['system_query', 'static_text'])->default('static_text');
            $table->string('action_key', 50)->nullable();
            $table->text('isi_balasan')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_rules');
    }
};
