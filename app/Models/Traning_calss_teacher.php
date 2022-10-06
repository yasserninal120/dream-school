<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traning_calss_teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'traning_class_id'
    ];


}
