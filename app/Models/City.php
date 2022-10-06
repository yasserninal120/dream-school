<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'CityName'
    ];
    function taning_class(){
        return $this->hasOne(Traning_class::class,'city_id');
    }
    public function samster(){
        return $this->hasOne(Samester::class,'city_id');
    }
    public function user(){
        return $this->hasOne(User::class,'city_id');
    }
    public function student(){
        return $this->hasOne(Student::class,'city_id');
    }
}
