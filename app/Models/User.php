<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uid',
        'name',
        'email',
        'phonenumber',
        'password',
        'role',
        'is_banned',
        'ban_reason',
        'banned_at',
        'banned_by',
    ];

    public function borrowRequests()
    {
        return $this->hasMany(BorrowRequest::class, 'users_id');
    }

    public function verificationRequest()
    {
        return $this->hasOne(VerificationRequest::class, 'users_id');
    }

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    // Ban-related methods
    public function isBanned()
    {
        return $this->is_banned == 1;
    }

    public function ban($reason, $bannedBy)
    {
        $this->update([
            'is_banned' => 1,
            'ban_reason' => $reason,
            'banned_at' => now(),
            'banned_by' => $bannedBy,
        ]);
    }

    public function unban()
    {
        $this->update([
            'is_banned' => 0,
            'ban_reason' => null,
            'banned_at' => null,
            'banned_by' => null,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Only set ip_address if the field exists in the database
            if (request() && in_array('ip_address', $model->getFillable())) {
                $model->ip_address = request()->ip();
            }
        });
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'banned_at' => 'datetime',
        ];
    }
}
