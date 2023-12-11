<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieDetails extends Model
{
    protected $table = 'movie_details';
    public $timestamps = false;
    // protected $primaryKey = '_id';
    protected $fillable =[
        '_id',
        'content',
        'type',
        'status',
        'is_copyright',
        'sub_docquyen',
        'trailer_url',
        'time',
        'episode_current',
        'episode_total',
        'quality',
        'lang',
        'notify',
        'showtimes',
        'view',
        'actor',
        'director',
        'category',
        'country'
    ];

    public function movies()
    {
        return $this->belongsTo(Movie::class, '_id', '_id');
    }
}


