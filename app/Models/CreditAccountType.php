<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditAccountType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function accounts()
    {
        return $this->hasMany(CreditAccount::class);
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
