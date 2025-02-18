<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['admin_id', 'user_id', 'action', 'asset_tag'];

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'admin_id');
    // }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function affectedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
