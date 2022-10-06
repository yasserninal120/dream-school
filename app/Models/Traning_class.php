<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traning_class extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'city_id'
    ];
    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }
    public function teacher(){
        return $this->belongsToMany(Teacher::class,'traning_calss_teachers','traning_class_id','teacher_id','id','id');
    }

}
