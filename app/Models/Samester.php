<?php

namespace App\Models;
use App\Models\Teacher;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\Sanctum;

class Samester extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',

    ];
    public function student(){
        return $this->hasOne(Samester::class,'samester_id');
    }
    public function teacher(){
        return $this->belongsToMany(Teacher::class,'teacher_samesters','semester_id','teacher_id','id','id');
    }

}
