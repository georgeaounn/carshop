<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\TypesSeeder;
use Database\Seeders\UsersSeeder;
use Database\Seeders\BrandsSeeder;
use Database\Seeders\PermissionsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            TypesSeeder::class,
            BrandsSeeder::class,
            RolesSeeder::class,
            UsersSeeder::class,
            PermissionsSeeder::class
        ]);
    }
}
