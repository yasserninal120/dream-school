<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class teacherSamester extends Model
{
    use HasFactory;
    protected $fillable = [
        'semester_id',
        'teacher_id'
    ];
}
