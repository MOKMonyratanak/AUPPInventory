<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $table = 'positions';
    protected $fillable = [
        'name',
        'description',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'position_id');
    }
}
