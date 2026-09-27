<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('gender', 10)->default('wanita');
            $table->string('photo_path', 150)->nullable();
            $table->string('tingkat_minyak', 10);
            $table->string('tingkat_kering', 10);
            $table->string('pori_pori', 10);
            $table->string('penggunaan_skincare', 10);
            $table->string('jerawat', 10);
            $table->string('sensitivitas', 10);
            $table->foreignId('predicted_skin_type_id')->nullable()->constrained('skin_types')->nullOnDelete();
            $table->json('probability_detail')->nullable(); // hasil probabilitas tiap kelas dari Naive Bayes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};