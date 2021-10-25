<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'outflow' => 'boolean'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}

