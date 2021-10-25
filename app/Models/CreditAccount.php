<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'balance' => 'float',
        'active' => 'boolean'
    ];

    public function credit_account_type()
    {
        return $this->belongsTo(CreditAccountType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
