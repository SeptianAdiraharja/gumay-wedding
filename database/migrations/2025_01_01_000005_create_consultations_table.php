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
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->default('wanita');
            $table->string('photo_path')->nullable();
            $table->string('tingkat_minyak');
            $table->string('tingkat_kering');
            $table->string('pori_pori');
            $table->string('penggunaan_skincare');
            $table->string('jerawat');
            $table->string('sensitivitas');
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
