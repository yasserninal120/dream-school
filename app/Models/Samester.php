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
        'city_id'

    ];
    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }
    public function student(){
        return $this->hasOne(Samester::class,'samester_id');
    }
    public function teacher(){
        return $this->belongsToMany(Teacher::class,'teacher_samesters','semester_id','teacher_id','id','id');
    }
    public function homwrk(){
        return $this->hasMany(Homwork::class,'semester_id');
    }
    public function obg(){
        return $this->hasMany(ObjectClass::class,'samester_id');
    }

}
