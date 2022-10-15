<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homwork extends Model
{
    protected $fillable = [
        'name_object',
        'contain_homwork',
        'semester_id',
        'user_id',
    ];
    public function howImage(){
        return $this->hasMany(ImageHomework::class, 'homwork_id');
     }

    public function semster(){
        return $this->belongsTo(Samester::class,'semester_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
