<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function getTotalHoursAttribute()
    {
        return $this->assignments()->sum('hours_per_week');
    }
}
