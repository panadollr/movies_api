<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $table = 'movies';
    public $timestamps = false;
    protected $primaryKey = '_id';
    protected $fillable =[
        'modified_time',
        '_id',
        'name',
        'origin_name',
        'thumb_url',
        'slug',
        'year',
        'poster_url',
    ];

}
