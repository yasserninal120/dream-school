<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectClass extends Model
{
    use HasFactory;
    protected $fillable = [
        'naem',
        'samester_id',
    ];
    public function city(){
        return $this->belongsTo(Samester::class , 'samester_id');
    }
    public function active(){
        return $this->hasMany(activetyObg::class,'object_class_id');
    }
    public function resulteActive(){
        return $this->hasMany(ResuletActive::class ,'object_class_id');
    }
}
