<?php

namespace App\Models;
use App\Models\Samester;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function semester(){
        return $this->belongsToMany(Samester::class,'teacher_samesters','teacher_id','semester_id','id','id');
    }
    public function traning(){
        return $this->belongsToMany(Traning_class::class,'traning_calss_teachers','teacher_id','traning_class_id','id','id');
    }
}
