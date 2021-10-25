<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ammount' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
