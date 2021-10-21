<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function accounts()
    {
        return $this->hasMany(CreditAccount::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function icons()
    {
        return $this->hasMany(Icon::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
}
