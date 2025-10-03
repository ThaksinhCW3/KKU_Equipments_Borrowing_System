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
    ];

    public function borrowRequests()
    {
        return $this->hasMany(BorrowRequest::class);
    }

    public function verificationRequest()
    {
        return $this->hasOne(VerificationRequest::class, 'users_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (request()) {
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
        ];
    }
}
