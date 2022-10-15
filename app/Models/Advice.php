<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advice extends Model
{
    use HasFactory;
    protected $fillable = [
        'advice',
        'user_id'
    ];

   public function user(){
    return $this->belongsTo(User::class,'user_id');
   }
}
