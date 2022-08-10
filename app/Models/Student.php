<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'samester_id',

    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function samester(){
        return $this-> belongsTo(Samester::class,'samester_id');
    }
    public function instalment(){
        return $this->hasOne(instalment::class,'student_id');
    }
    public function morningCheck(){
        return $this-> hasMany(Student::class,'student_id');
    }

}
