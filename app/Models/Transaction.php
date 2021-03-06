<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function credit_account()
    {
        return $this->belongsTo(CreditAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
