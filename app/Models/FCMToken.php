<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FCMToken extends Model
{
    use HasFactory;
    public $table = 'fcm_tokens';
    protected $fillable = [
        'user_id',
        'token'
    ];

    private function user(){
        return $this->belongsTo(User::class);
    }
}
