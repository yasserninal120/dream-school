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
        'city_id'
    ];
   public function city(){
       return $this->belongsTo(User::class,'city_id');
   }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function samester(){
        return $this-> belongsTo(Samester::class,'samester_id');
    }

    public function morningCheck(){
        return $this-> hasMany(MorningCheckUp::class,'student_id');
    }
    public function note(){
        return $this->hasMany(Note::class ,'student_id');
    }
    public function k(){
        return $this->hasMany(takeyem_students::class , '');
    }
    public function takeym(){
        return $this->belongsToMany(takeyem_students::class,'takeyem_students','student_id','takeyem_id','id','id');
    }

    public function resulteActive(){
        return $this->hasMany(ResuletActive::class ,'student_id');
    }
}
