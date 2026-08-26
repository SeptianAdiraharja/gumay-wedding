<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SkinTypeSeeder::class,
            SkinAttributeSeeder::class,
            TrainingDatasetSeeder::class,
            MakeupRecommendationSeeder::class,
            AdminSeeder::class,
        ]);
    }
}