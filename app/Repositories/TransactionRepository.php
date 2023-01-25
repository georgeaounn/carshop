<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;

class TransactionRepository
{

// == GET

    // ----- get transaction by id
    function getTransactionById($transaction_id)
    {
        return Transaction::where('id', $transaction_id)->with(['cars'])->first();
    }

    // ----- get all transactions
    function getAllTransactions($request)
    {
        $transactions = Transaction::with(['cars']);


        if(isset($request->user_ids) && count($request->user_ids))
        {
            $transactions = $transactions->whereIn('user_id', $request->user_ids);
        }

        if(isset($request->minimum_date))
        {
            $transactions = $transactions->where('date', '>=', $request->minimum_date);
        }

        return isset($request->per_page) ? $transactions->paginate($request->per_page) : $transactions->get();
    }

//

// == EDIT

    // ----- create transaction
    function createTransaction($request)
    {
        $transaction = Transaction::create(array_merge([
            "amount" => $request->amount,
            "user_id" => Auth::user()->id,
            "date" => Carbon::now()
        ]));

        foreach($request->cars_array as $car_details)
        {
            $transaction->cars()->attach($car_details['car_id'], ["price" => $car_details['car']->price, "quantity" => $car_details['quantity']]);
        }

    }

    // ----- export transactions
    function exportTransactions()
    {
        $transactions = Transaction::join('users', 'users.id', '=', 'transactions.user_id')->select('transactions.*', 'users.name', 'users.email', 'users.phone_number', 'users.date_of_birth')->get();
        return (new FastExcel($transactions))->download( 'transactions'. Carbon::now() .'xlsx');
    }
//

// == DELETE

    // ---- delete car
    function deleteCar($id)
    {
        return Car::where('id', $id)->delete();
    }

//

}
