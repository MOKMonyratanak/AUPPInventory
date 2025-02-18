<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    use HasFactory;
    protected $table = 'device_types';
    protected $fillable = [
        'name',
        'description',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'device_type_id');
    }
}
