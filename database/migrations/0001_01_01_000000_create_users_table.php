<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 60); // bcrypt hash selalu 60 karakter
            $table->rememberToken(); // default 100, sudah standar Laravel
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 100)->primary();
            $table->string('token', 100);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 40)->primary(); // session id Laravel = 40 karakter
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable(); // sudah pas (cukup untuk IPv6)
            $table->text('user_agent')->nullable(); // text sudah tepat, tidak perlu diubah
            $table->longText('payload'); // sudah tepat, payload sesi bisa besar
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};