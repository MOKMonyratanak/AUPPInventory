<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    // Define the table name if it's different from the plural of the model name (optional)
    protected $table = 'brands';
    protected $fillable = [
        'name',
        'description',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'brand_id');
    }
}
