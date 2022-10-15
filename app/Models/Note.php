<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'student_id',
        'note',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function student(){
        return $this->belongsTo(Student::class,'student_id');
    }
    public function images(){
        return $this->hasMany(ImageNote::class,'note_id');
    }




}
