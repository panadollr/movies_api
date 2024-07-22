<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    protected static function boot()
    {
        parent::boot();

        // Thêm global scope để chỉ lấy Manga có ít nhất một Chapter
        static::addGlobalScope('countryScope', function (Builder $builder) {
            $countries = ['Trung Quốc', 'Đài Loan', 'Hồng Kông'];

            $builder->where(function($query) use ($countries) {
                foreach ($countries as $country) {
                    $query->orWhereRaw('JSON_CONTAINS(country, \'{"name": "' . $country . '"}\')');
                }
            });
        });

        // Thêm global scope để chỉ lấy Movie ngoại trừ một số danh mục
        static::addGlobalScope('excludeCategories', function (Builder $builder) {
            $excludedCategories = ['âm nhạc', 'tài liệu', '18+'];

            $builder->where(function ($query) use ($excludedCategories) {
                foreach ($excludedCategories as $category) {
                    $query->whereRaw('NOT JSON_CONTAINS(category, \'{"name": "' . $category . '"}\')');
                }
            });
        });
    }


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

