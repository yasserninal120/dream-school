<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPay extends Model
{
    use HasFactory;
    protected $fillable = [
        'pay',
        'date',
        'user_id'

    ];
    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

}
