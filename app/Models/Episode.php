<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $table = 'episodes';
    public $timestamps = false;
    // protected $primaryKey = 'movie_id';
    protected $fillable =[
        '_id',
        'slug',
        'server_1',
        'server_2',
        'server_3'
    ];
}
