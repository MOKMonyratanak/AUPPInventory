<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    use HasFactory;

    // Define the table name if it's different from the plural of the model name (optional)
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
