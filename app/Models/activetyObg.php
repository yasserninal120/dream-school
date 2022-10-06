<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class activetyObg extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'object_class_id'
    ];
    public function obg(){
        return $this->belongsTo(ObjectClass::class,'object_class_id');
    }
    public function resulteActive(){
        return $this->hasMany(ResuletActive::class ,'activety_obg_id');
    }
}
