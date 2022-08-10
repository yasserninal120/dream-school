<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MorningCheckUp extends Model
{
    use HasFactory;
    protected $fillable = [
        'checkUp',
        'student_id'
    ];
    public function student(){
        return $this-> belongsTo(Student::class,'student_id');
    }
}
