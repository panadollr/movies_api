<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieDetails extends Model
{
    protected $table = 'movie_details';
    public $timestamps = false;
    // protected $primaryKey = '_id';
    // protected $fillable =[
    //     '_id',
    //     'content',
    //     'type',
    //     'status',
    //     'is_copyright',
    //     'sub_docquyen',
    //     'trailer_url',
    //     'time',
    //     'episode_current',
    //     'episode_total',
    //     'quality',
    //     'lang',
    //     'notify',
    //     'showtimes',
    //     'view',
    //     'actor',
    //     'director',
    //     'category',
    //     'country'
    // ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function movie()
    {
        return $this->belongsTo(Movie::class, '_id', '_id');
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class, '_id', '_id');
    }
}


