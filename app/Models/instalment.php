<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class instalment extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'discointUsdOrPersent',
        'transport',
        'instalment',
    ];

    public function student(){
        return $this->belongsTo(Student::class,'student_id');
    }

    public function pay(){
        return $this->hasMany(pay::class,'instalment_id');
    }

}
