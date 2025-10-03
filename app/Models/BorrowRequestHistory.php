<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRequestHistory extends Model
{
    protected $table = 'borrow_request_histories';
    
    protected $fillable = [
        'borrow_request_id',
        'old_status',
        'new_status',
        'old_data',
        'new_data',
        'action',
        'admin_id',
        'notes'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    // Relationships
    public function borrowRequest()
    {
        return $this->belongsTo(BorrowRequest::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}