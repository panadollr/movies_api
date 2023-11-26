<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episodes extends Model
{
    protected $table = 'episodes';
    public $timestamps = false;
    protected $primaryKey = 'movie_id';
    protected $fillable =[
        'server_name',
        'movie_id',
        'server_data'
    ];
}
