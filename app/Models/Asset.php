<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $primaryKey = 'asset_tag';  // Specify primary key if it's not 'id'
    public $incrementing = false;  // Disable auto-incrementing
    protected $keyType = 'string';  // Set key type to string

    protected $fillable = [
        'asset_tag',
        'device_type_id',  // Reflect the updated column name
        'brand_id',
        'model',
        'serial_no',
        'company_id',
        'condition',
        'status',
        'user_id',
        'checked_out_by',
        'purpose',
        'note'
    ];

    /**
     * Relationship with User (for 'user_id')
     * User to whom the asset is assigned.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with User (for 'checked_out_by')
     * User who checked out the asset.
     */
    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    /**
     * Relationship with Company
     * The company that owns the asset.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relationship with DeviceType
     * The type of device that the asset represents.
     */
    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'asset_id');
    }

    public function latestIssueLog()
    {
        return $this->hasOne(ActivityLog::class, 'asset_tag', 'asset_tag')
            ->where('action', 'issue')
            ->latest();
    }
}
