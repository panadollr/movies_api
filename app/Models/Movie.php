<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function movie_details()
    {
        return $this->hasOne(MovieDetails::class, '_id', '_id'); // Thay 'foreign_key' bằng khóa ngoại thực tế
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class, '_id', '_id'); // Thay 'foreign_key' bằng khóa ngoại thực tế
    }
}

