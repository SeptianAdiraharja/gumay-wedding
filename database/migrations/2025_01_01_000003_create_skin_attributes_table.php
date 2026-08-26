<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skin_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('attribute_key')->unique(); // tingkat_minyak, tingkat_kering, sensitivitas, jerawat, pori_pori
            $table->string('question_text');            // Teks pertanyaan ke pengguna
            $table->json('options');                     // Pilihan jawaban, mis. ["rendah","sedang","tinggi"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skin_attributes');
    }
};
