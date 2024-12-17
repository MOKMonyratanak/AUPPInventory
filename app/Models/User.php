<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Define the table associated with the model
    protected $table = 'users';

    // Define the fillable fields (columns that can be mass-assigned)
    protected $fillable = [
        'id',
        'name',               // User's name
        'email',              // User's email
        'role',               // User's role (e.g., user, manager, admin)
        'company_id',         // Foreign key referencing the company table
        'position',           // User's position in the company
        'contact_number',     // User's contact number
        'status',             // User's status (employed or resigned)
        'password',           // User's password
        'remember_token',     // Token for "remember me" functionality
    ];

    // Define auto-incrementing and keyType if necessary
    public $incrementing = false;
    protected $keyType = 'int';

    // Relationship with the Asset model (One-to-many relationship)
    public function assets()
    {
        return $this->hasMany(Asset::class, 'user_id', 'id');
    }

    // Relationship with the Company model (Many-to-one relationship)
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

}

