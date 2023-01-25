<?php

namespace App\Validations;

use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarValidation
{
    function validateCreateCar()
    {
        return Validator::make(request()->all(),[
            "name" => "required|string",
            "automatic_transmission" => "required|boolean",
            "color" => "required|string",
            "fuel_consumption" => "required|integer|min:0",
            "mileage" => "required|integer|min:0",
            "price" => "required|integer|min:0",
            "quantity" => "required|integer|min:0",
            "type_id" => "required|integer|exists:types,id",
            "brand_id" => "required|integer|exists:brands,id",
        ]);
    }

    function validateUpdateCar($id)
    {
        request()->merge(
            ["id" => $id]
        );
        return Validator::make(request()->all(), [
            "id" => "required|integer|exists:cars,id,deleted_at,NULL",
            "name" => "required|string",
            "automatic_transmission" => "required|boolean",
            "color" => "required|string",
            "fuel_consumption" => "required|integer|min:0",
            "mileage" => "required|integer|min:0",
            "price" => "required|integer|min:0",
            "quantity" => "required|integer|min:0",
            "type_id" => "required|integer|exists:types,id",
            "brand_id" => "required|integer|exists:brands,id",
        ]
    );
    }

    function validateCarId($id)
    {
        request()->merge([
            "id" => $id
        ]);
        return Validator::make(request()->all(), [
            "id" => "required|integer|exists:cars,id,deleted_at,NULL"
        ]);
    }

    function validateGetAllCars()
    {
        return Validator::make(request()->all(), [
            "name" => "nullable|string",
            "minimum_price" => "nullable|integer",
            "max_price" => "nullable|integer",
            "brand_ids" => "nullable|array",
            "brand_ids.*" => "required|integer",
            "type_ids" => "nullable|array",
            "type_ids.*" => "required|integer",
            "per_page" => "nullable|integer"
        ]);
    }

    function validateGetAllBrands()
    {
        return Validator::make(request()->all(), [
            "name" => "nullable|string",
            "per_page" => "nullable|integer"
        ]);
    }

}
