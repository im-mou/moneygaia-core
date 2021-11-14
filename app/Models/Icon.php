<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "enabled" => "boolean",
    ];

    const ACCOUNTS_GROUP = "accounts";
    const TRANSACTIONS_GROUP = "transactions";
    const GOALS_GROUP = "goals";

    const ALLOWED_GROUPS = [self::ACCOUNTS_GROUP, self::TRANSACTIONS_GROUP, self::GOALS_GROUP];
}
