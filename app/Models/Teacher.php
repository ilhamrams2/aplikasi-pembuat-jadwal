<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function homeroomClass()
    {
        return $this->hasOne(Classroom::class, 'homeroom_teacher_id');
    }

    public function getTotalTeachingHoursAttribute()
    {
        return $this->assignments()->sum('hours_per_week');
    }
}
