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
        'movie_id',
        'server_name',
        'server_data'
    ];
}
