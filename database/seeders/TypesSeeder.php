<?php

namespace Database\Seeders;

use Exception;
use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $name = DB::select('select * from seeders where name=?', ["TypesSeeder"]);
            if (!isset($name[0]->name)) {
                $this->command->warn("Types Seeder Started");
                $json = file_get_contents('database/data/seeds/types.json');
                $data = json_decode($json);
                $total = count($data);
                $counter = 0;

                foreach ($data as $value) {
                    $counter++;
                    Type::create([
                        "name" => $value->name
                    ]);

                    $this->command->info(($counter * 100) / $total);
                }
                DB::insert('insert into seeders (name) values (?)', ["TypesSeeder"]);
                $this->command->warn("Types Seeder Ended");
            } else {
                $this->command->warn("Types Seeder already exist.");
            }
        } catch (Exception $ex) {
            $this->command->error($ex->getMessage());
        }
    }
}
