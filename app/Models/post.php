<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\comment;
use App\Models\like;
class post extends Model
{
    use HasFactory;
    protected $fillable = [
        'body',
        'user_id',
        'image'
    ];
 public function postImage(){
    return $this->hasMany(Image_Post::class, 'post_id');
 }
 public function user(){
     return $this->belongsTo(User::class,'user_id');
 }
 public function comments(){
     return $this->hasMany(comment::class,'post_id');
 }

 public function likes(){
     return $this->hasMany(like::class,'post_id');
 }

}
