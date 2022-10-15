<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageHomework extends Model
{
    use HasFactory;
    protected $fillable = [
        'homwork_id',
        'urlImage'
    ];
    public function homwork(){
        return  $this->belongsTo(Homwork::class,'homwork_id');
    }
}
