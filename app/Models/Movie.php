<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $table = 'movies';
    public $timestamps = false;
    // protected $primaryKey = '_id';
    protected $searchable = ['name', 'slug'];
    
    // protected $fillable =[
    //     'modified_time',
    //     '_id',
    //     'name',
    //     'origin_name',
    //     'thumb_url',
    //     'slug',
    //     'year',
    //     'poster_url'
    // ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function movie_detail()
    {
        return $this->hasOne(MovieDetails::class, '_id', '_id');
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class, '_id', '_id');
    }
}

