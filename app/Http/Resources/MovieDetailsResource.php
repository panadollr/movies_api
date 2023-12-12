<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        $imageDomain = config('api_settings.image_domain');
        $movie = $this['movie'];

        return [
            'movie' => [
                'modified_time' => $movie['modified_time'],
                'id' => $movie['_id'],
                'name' => $movie['name'],
                'slug' => $movie['slug'],
                'origin_name' => $movie['origin_name'],
                'content' => $movie['content'],
                'type' => $movie['type'],
                'status' => $movie['status'],
                'thumb_url' => $imageDomain . $movie['poster_url'],
                'poster_url' => $imageDomain . $movie['thumb_url'],
                'is_copyright' => $movie['is_copyright'],
                'sub_docquyen' => (bool) $movie['sub_docquyen'],
                'trailer_url' => $movie['trailer_url'],
                'time' => $movie['time'],
                'episode_current' => $movie['episode_current'],
                'episode_total' => $movie['episode_total'],
                'quality' => $movie['quality'],
                'lang' => $movie['lang'],
                'notify' => $movie['notify'],
                'showtimes' => $movie['showtimes'],
                'year' => $movie['year'],
                'view' => $movie['view'],
                'actor' => json_decode($movie['actor']),
                'director' => json_decode($movie['director']),
                'category' => $this->formattedCategoriesArray($movie, 'category'),
                // 'country' => $this->formattedArray($movie, 'country'),
            ],
            'episodes' => $this['episodes'],
        ];
    }

    protected function formattedArray($movie, $propertyName)
    {
        $propertyValue = $movie[$propertyName];

        return array_map(function ($item) {
                return ['name' => $item['name']];
            }, json_decode($propertyValue, true));
    }


    protected function formattedCategoriesArray($movie, $propertyName)
    {
        $propertyValue = $movie[$propertyName];
        $categories = [
            'hanh-dong' => 'Hành Động',
            'tinh-cam' => 'Tình Cảm',
            'hai-huoc' => 'Hài Hước',
            'co-trang' => 'Cổ Trang',
            'tam-ly' => 'Tâm lý',
            'hinh-su' => 'Hình Sự',
            'chien-trang' => 'Chiến Trang',
            'the-thao' => 'Thể Thao',
            'vo-thuat' => 'Võ Thuật',
            'vien-tuong' => 'Viễn Tưởng',
            'phieu-luu' => 'Phiêu Lưu',
            'khoa-hoc' => 'Khoa Học',
            'kinh-di' => 'Kinh Dị',
            'am-nhac' => 'Âm Nhạc',
            'than-thoai' => 'Thần Thoại',
            'tai-lieu' => 'Tài Liệu',
            'gia-dinh' => 'Gia Đình',
            'chinh-kich' => 'Chính Kịch',
            'bi-an' => 'Bí Ẩn',
            'hoc-duong' => 'Học Đường',
            'kinh-dien' => 'Kinh Điển',
            'phim-18' => 'Phim 18+'
        ];
    
        return array_map(function ($item) use ($categories) {
                foreach ($categories as $category_slug => $category_name) {
                    if ($item['slug'] == $category_slug) {
                        return ['name' => $category_name];
                    }
                }
            }, json_decode($propertyValue, true));
    }

}
