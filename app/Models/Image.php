<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';
    public $timestamps = false;
    // protected $primaryKey = 'movie_id';
    protected $fillable =[
        '_id',
        'poster_url',
        'thumb_url'
    ];
}
