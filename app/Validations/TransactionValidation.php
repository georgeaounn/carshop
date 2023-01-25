<?php

namespace App\Validations;

use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionValidation
{
    function validateGetAllTransactions()
    {
        return Validator::make(request()->all(),[
            "user_ids" => "nullable|array",
            "user_ids.*" => "required|integer",
            "per_page" => "nullable|integer",
            "minimum_date" => "nullable|string"
        ]);
    }

    function validateCreateTransaction()
    {
        return Validator::make(request()->all(), [
            "cars" => "required|array",
            "cars.*.car_id" => "required|integer|exists:cars,id,deleted_at,NULL|distinct",
            "cars.*.quantity" => "required|integer|min:0"
        ]
    );
    }

    function validateTransactionId($id)
    {
        request()->merge([
            "id" => $id
        ]);
        return Validator::make(request()->all(), [
            "id" => "required|integer|exists:transactions,id"
        ]);
    }


}
