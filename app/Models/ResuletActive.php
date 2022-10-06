<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResuletActive extends Model
{
    use HasFactory;
    protected $fillable = [
        'marek',
        'student_id',
        'object_class_id',
        'activety_obg_id',
    ];

   public function student(){
    return $this->belongsTo(Student::class,'student_id');
   }
   public function obg(){
    return $this->belongsTo(ObjectClass::class,'object_class_id');
   }
   public function active(){
    return $this->belongsTo(activetyObg::class,'activety_obg_id');
   }



}
