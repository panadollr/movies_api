<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\MovieDetails;

class Movie extends Model
{
    protected $table = 'movies';
    public $timestamps = false;
    // protected $primaryKey = '_id';
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

    public function movie_details()
    {
        return $this->hasMany(MovieDetails::class, "_id", "_id");
    }

}

