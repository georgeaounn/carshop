<?php

namespace Database\Seeders;

use Exception;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $name = DB::select('select * from seeders where name=?', ["UsersSeeder"]);
            if (!isset($name[0]->name)) {
                $this->command->warn("Users Seeder Started");
                $json = file_get_contents('database/data/seeds/users.json');
                $data = json_decode($json);
                $total = count($data);
                $counter = 0;

                foreach ($data as $value) {
                    $counter++;
                    $user = User::create([
                        "name" => $value->name,
                        "email" => $value->email,
                        "password" => bcrypt($value->password),
                        "phone_number" => $value->phone_number,
                        "role_id" => $value->role_id,
                        "date_of_birth" => $value->date_of_birth
                    ]);

                    $user->assignRole($value->role_id);

                    $this->command->info(($counter * 100) / $total);
                }
                DB::insert('insert into seeders (name) values (?)', ["UsersSeeder"]);
                $this->command->warn("Users Seeder Ended");
            } else $this->command->warn("Users Seeder already exist.");
        } catch (Exception $ex) {
            $this->command->error($ex->getMessage());
        }
    }
}
