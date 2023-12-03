<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable =[
        'title',
        'poster_url',
        'content',
        'movie_type',
        'date'
    ];
}
