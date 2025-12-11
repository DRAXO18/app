<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_security', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->unique(); // 1 a 1 real con users

            // ===========================
            // 🔐 FUERZA BRUTA Y BLOQUEOS
            // ===========================
            $table->unsignedTinyInteger('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            // ===========================
            // 🧾 AUDITORÍA DE ACCESOS
            // ===========================
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_ip')->nullable();
            $table->string('last_user_agent')->nullable();

            // ===========================
            // 🚨 MONITOREO DE ATAQUES
            // ===========================
            $table->timestamp('last_failed_at')->nullable();
            $table->ipAddress('last_failed_ip')->nullable();

            // ===========================
            // 🧬 SEGURIDAD DE SESIÓN
            // ===========================
            $table->string('last_token_id')->nullable();
            // para invalidar sesiones antiguas si quieres

            // ===========================
            // ⚠️ CONTROL DE DISPOSITIVOS
            // ===========================
            $table->string('device_fingerprint')->nullable();

            $table->timestamps();

            // ===========================
            // ✅ ÍNDICES CRÍTICOS
            // ===========================
            $table->index('failed_attempts');
            $table->index('locked_until');
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_security');
    }
};
