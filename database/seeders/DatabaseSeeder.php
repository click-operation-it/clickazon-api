<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\PermissionsTableSeeder;
use SbscPackage\Ecommerce\Database\Seeders\DatabaseSeeder as EcommerceDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EcommerceDatabaseSeeder::class,
        ]);
    }
}
