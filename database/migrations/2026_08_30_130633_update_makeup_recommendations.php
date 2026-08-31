<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('makeup_recommendations', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'category']);

            $table->boolean('is_acne')->default(false)->after('skin_type_id');
            $table->boolean('is_sensitive')->default(false)->after('is_acne');
            $table->text('tips_perawatan')->after('is_sensitive');
            $table->text('makeup_perempuan')->after('tips_perawatan');
            $table->text('makeup_laki_laki')->after('makeup_perempuan');

            $table->unique(['skin_type_id', 'is_acne', 'is_sensitive'], 'makeup_reco_combo_unique');
        });
    }

    public function down(): void
    {
        Schema::table('makeup_recommendations', function (Blueprint $table) {
            $table->dropUnique('makeup_reco_combo_unique');
            $table->dropColumn(['is_acne', 'is_sensitive', 'tips_perawatan', 'makeup_perempuan', 'makeup_laki_laki']);

            $table->string('title')->after('skin_type_id');
            $table->text('description')->nullable()->after('title');
            $table->string('category')->after('description');
        });
    }
};