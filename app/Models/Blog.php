<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable =[
        'title',
        'slug',
        'poster_url',
        'thumb_url',
        'content',
        'movie_type',
        'date'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            $blog->id = static::generateNewId();
        });
    }

    protected static function generateNewId()
    {
        // Thực hiện logic để sinh id tự động tăng mới ở đây
        // Ví dụ: Tìm id lớn nhất hiện tại, sau đó tăng thêm 1
        return static::max('id') + 1;
    }
}
