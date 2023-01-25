<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $name = DB::select('select * from seeders where name=?', ["RolesSeeder"]);
            if (!isset($name[0]->name)) {
                $this->command->warn("Roles Seeder Started");
                $json = file_get_contents('database/data/seeds/roles.json');
                $data = json_decode($json);
                $total = count($data);
                $counter = 0;

                foreach ($data as $value) {
                    $counter++;
                    Role::create([
                        "name" => $value->name
                    ]);

                    $this->command->info(($counter * 100) / $total);
                }
                DB::insert('insert into seeders (name) values (?)', ["RolesSeeder"]);
                $this->command->warn("Roles Seeder Ended");
            } else $this->command->warn("Roles Seeder already exist.");
        } catch (Exception $ex) {
            $this->command->error($ex->getMessage());
        }
    }
}
