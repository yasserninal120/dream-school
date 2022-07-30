<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pay extends Model
{
    use HasFactory;
    protected $fillable = [
        'pay',
        'instalment_id'
    ];

    public function instalment(){
        return $this-> belongsTo(instalment::class,'instalment_id');
    }


}
