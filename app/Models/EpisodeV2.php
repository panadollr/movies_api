<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpisodeV2 extends Model
{
    protected $table = 'episodes_v2';
    protected $primaryKey = 'id';
    protected $fillable =[
        'movie_id',
        'slug',
        'type',
        'server_id'
    ];
}
