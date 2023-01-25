<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->command->warn("PermissionsSeeder Started");
            $json = file_get_contents('database/data/seeds/permissions.json');
            $permissions = json_decode($json);
            $total = count($permissions);
            $counter = 0;

            foreach ($permissions as $value) {
                $counter++;
                $exist_permision = Permission::where('name', $value->name)->first();
                if ($exist_permision) {
                    $exist_permision->syncRoles($value->roles);
                } else {
                    $permissions = Permission::create([
                        "name" => $value->name
                    ]);
                    $permissions->syncRoles($value->roles);
                }
                $this->command->info(($counter * 100) / $total);
            }
            $this->command->warn("PermissionsSeeder Ended");
        } catch (Exception $ex) {
            $this->command->error($ex->getMessage());
        }
    }
}
