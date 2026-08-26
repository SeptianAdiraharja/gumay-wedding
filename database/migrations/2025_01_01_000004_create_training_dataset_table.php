<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_dataset', function (Blueprint $table) {
            $table->id();
            $table->string('tingkat_minyak');       // rendah / sedang / tinggi
            $table->string('tingkat_kering');       // rendah / sedang / tinggi
            $table->string('pori_pori');            // kecil / sedang / besar
            $table->string('penggunaan_skincare');   // ya / tidak
            $table->string('jerawat');              // ya / tidak
            $table->string('sensitivitas');         // rendah / sedang / tinggi
            $table->foreignId('skin_type_id')->constrained('skin_types')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_dataset');
    }
};
