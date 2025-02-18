<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'users';

    protected $fillable = [
        'id',
        'name',
        'email',
        'role',
        'company_id',
        'position_id',
        'contact_number',
        'status',
        'password',
        'remember_token',
    ];

    public $incrementing = false;
    protected $keyType = 'int';

    public function assets()
    {
        return $this->hasMany(Asset::class, 'user_id', 'id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

}

