<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pay extends Model
{
    use HasFactory;
    protected $fillable = [
        'pay',
        'user_id',
        'created_at'  => 'datetime:Y-m-d H:i',
    ];

    public function user(){
        return $this-> belongsTo(User::class,'user_id');
    }


}
