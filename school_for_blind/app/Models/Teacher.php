<?php

namespace App\Models;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Teacher extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects')
            ->withPivot('price_for_lesson')
            ->withTimestamps();
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_teacher', 'teacher_id', 'class_id');
    }

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
        // 'cv_path',
        'notifications_enabled'
    ];
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
        static::deleting(function ($teacher) {
            $teacher->deviceTokens()->delete();
            $teacher->notifications()->delete();
        });
    }
 public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    public function reportsMade()
    {
        return $this->morphMany(Report::class, 'reporter');
    }

    public function reportsReceived()
    {
        return $this->morphMany(Report::class, 'reported');
    }

    public function punishments()
    {
        return $this->morphToMany(Punishment::class, 'punishable', 'punishables')
            ->using(Punishable::class)
            ->withPivot(['id', 'admin_id', 'expires_at'])
            ->withTimestamps();
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }


    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function availabilities()
    {
        return $this->hasMany(TeacherAvailability::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByLevel($query, $level)
    {
        if ($level && in_array($level, ['ninth', 'twelfth'])) {
            return $query->where('level', $level);
        }
        return $query;
    }

    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }
        return $query;
    }

}

