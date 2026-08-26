<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE galleries MODIFY category ENUM('makeup','dekor','dokumentasi','busana_pengantin','sertifikat') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE galleries MODIFY category ENUM('makeup','dekor','dokumentasi','busana_pengantin') NOT NULL");
        }
    }
};