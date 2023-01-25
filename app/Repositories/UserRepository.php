<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserRepository
{


// == GET

    // ----- get user by id
    function getUserById($user_id)
    {
        return User::where('id', $user_id)->with('role')->with('transactions.cars')->first();
    }

    // ----- get all users
    function getAllUsers($request)
    {
        $users = User::where('id', '<>', Auth::user()->id);

        if ($request->name) {
            $users = $users->where(function ($query) use ($request) {
                $query->where('name', 'ILIKE', '%' . $request->name . '%')
                    ->orWhere('email', 'ILIKE', '%' . $request->name . '%');
            });
        }

        if(isset($request->sort))
        {
            switch($request->sort){
                case 1 : $users = $users->orderBy('id', "DESC");
                        break;
                case 2 : $users = $users->orderBy('id', "ASC");
                        break;
                case 3 : $users = $users->orderBy('name',"ASC");
                        break;
                case 4 : $users = $users->orderBy('name',"DESC");
                        break;
            }
        }


        return isset($request->per_page) ? $users->paginate($request->per_page) : $users->get();
    }

//

// == EDIT

    // ----- update password
    function updatePassword($request)
    {
        return User::where(['email' => $request->email, 'user_type_id' => $request->user_type_id])->update([
            'password' => bcrypt($request->password)
        ]);
    }

    // ----- create user
    function createUser($request)
    {
        $user = User::create(array_merge([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'role_id' => 2,
        ]));

        $user->assignRole(2);
        return $user;
    }

    // ----- change password
    function changePassword($request)
    {
        return User::where('id', Auth::user()->id)->update([
            "password" => bcrypt($request->password)
        ]);
    }

    // ----- update self user
    function updateUser($request)
    {
        return User::where('id', $request->user_id)->update([
            'name' => $request->full_name,
            'email' => $request->country_id,
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth
        ]);
    }
//

}
