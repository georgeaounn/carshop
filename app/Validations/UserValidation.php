<?php

namespace App\Validations;

use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserValidation
{
    function loginValidation()
    {
        return Validator::make(request()->all(),[
            "email" => "required|string|email|exists:users,email",
            "password" => "required|string"
        ]);
    }

    function validateRegistration()
    {
        return Validator::make(request()->all(), [
            "name" => "required|string",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:8",
            "phone_number" => "required|string",
            "date_of_birth" => "required|date|before:now"
        ]
    );
    }

    function validateUserId($id)
    {
        request()->merge([
            "id" => $id
        ]);
        return Validator::make(request()->all(), [
            "id" => "required|integer|exists:users,id"
        ]);
    }

    function validateChangePassword()
    {
        return Validator::make(request()->all(), [
            "current_password" => "required|string",
            "password" => "required|string",
        ]);
    }

    function validateGetAllUsers()
    {
        return Validator::make(request()->all(), [
            "name" => "nullable|string",
            "per_page" => "nullable|integer"
        ]);
    }

    function validateUpdateSelfUser()
    {
        return Validator::make(request()->all(), [
            "name" => "required|string",
            "email" => "required|email|unique:users,email,". Auth::user()->id,
            "phone_number" => "required|string",
            "date_of_birth" => "required|date|before:now"
        ]);
    }

}
