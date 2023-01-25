<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'amount',
        'date',
        'user_id'
    ];

    public function cars()
    {
        return $this->belongsToMany(Car::class, 'transaction_cars')->withPivot('price', 'quantity')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
