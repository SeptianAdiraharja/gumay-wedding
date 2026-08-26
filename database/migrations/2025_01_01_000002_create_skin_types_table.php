<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skin_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // normal, oily, dry, combination, sensitive, acne
            $table->string('name');           // Nama tampil, mis. "Kulit Berminyak"
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skin_types');
    }
};
