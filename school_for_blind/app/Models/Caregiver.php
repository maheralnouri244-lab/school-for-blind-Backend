<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Caregiver extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\CaregiverFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'phone',
        'password',
        'fcm_token',
        'notifications_enabled'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',

        ];
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
    public function donations()
    {
        return $this->morphMany(Donation::class, 'donatable');
    }

    public function deviceTokens()
    {
        return $this->morphMany(DeviceToken::class, 'tokenable');   
    }
    protected static function booted()
    {
        static::deleting(function ($caregiver) {
            $caregiver->deviceTokens()->delete();
            $caregiver->notifications()->delete();
        });
    }
   public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }
    
}