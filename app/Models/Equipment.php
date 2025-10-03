<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Equipment extends Model
{
    protected $table = 'equipments';
    protected $fillable = [
        'code',
        'name',
        'description',
        'categories_id',
        'status',
        'photo_path',
        'accessories'
    ];

    protected $casts = [
        'accessories' => 'array',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id', 'id');
    }

    /**
     * Get total quantity of this equipment type
     */
    public function getTotalQuantityAttribute()
    {
        return static::where('name', $this->name)->count();
    }

    /**
     * Get available quantity of this equipment type
     */
    public function getAvailableQuantityAttribute()
    {
        return static::where('name', $this->name)->where('status', 'available')->count();
    }

    /**
     * Get borrowed quantity of this equipment type
     */
    public function getBorrowedQuantityAttribute()
    {
        return static::where('name', $this->name)->where('status', 'borrowed')->count();
    }
}
