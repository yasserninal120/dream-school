<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image_Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'urlImage'
    ];
    public function post(){
        return  $this->belongsTo(post::class,'post_id');
    }
}
