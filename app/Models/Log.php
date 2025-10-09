<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $table = 'logs';
    
    protected $fillable = [
        'admin_id',
        'action',
        'target_name',
        'target_type',
        'target_id',
        'description',
        'old_values',
        'new_values',
        'user_agent',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }


    /**
     * Scope for filtering by target type
     */
    public function scopeByTargetType($query, $targetType)
    {
        return $query->where('target_type', $targetType);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('admin_id', $userId);
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        $action = ucfirst($this->action);
        $target = $this->target_name ? " for {$this->target_name}" : '';
        
        return "{$action} action performed{$target}";
    }


    /**
     * Get action badge class
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match($this->action) {
            'create' => 'bg-green-100 text-green-800',
            'update' => 'bg-blue-100 text-blue-800',
            'delete' => 'bg-red-100 text-red-800',
            'login' => 'bg-green-100 text-green-800',
            'logout' => 'bg-gray-100 text-gray-800',
            'approve' => 'bg-green-100 text-green-800',
            'reject' => 'bg-red-100 text-red-800',
            'cancel' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
