<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Takeyem extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'value',
        'image'
    ];
    public function student(){
        return $this->belongsToMany(takeyem_students::class,'takeyem_students','takeyem_id','student_id','id','id');
    }
}
