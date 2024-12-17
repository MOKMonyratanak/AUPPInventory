<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['checked_out_by', 'user_id', 'action', 'asset_tag', 'purpose'];

    // Relationship to the user who performed the action
    public function user()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function affectedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
