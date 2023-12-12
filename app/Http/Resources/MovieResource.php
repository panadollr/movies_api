<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;

use App\Models\MovieDetails;


class MovieResource extends JsonResource
{
    public function toArray($request)
    {
        $imageDomain = config('api_settings.image_domain');

        return [
            // 'modified_time' => (new DateTime($this->modified_time))->format('m/Y'),
            'modified_time' => $this->modified_time,
            'id' => $this->_id,
            'name' => $this->name,
            'origin_name' => $this->origin_name,
            'thumb_url' => $this->formatImageUrl($this->poster_url, $imageDomain),
            'slug' => $this->slug,
            'year' => $this->year,
            'poster_url' => $this->formatImageUrl($this->thumb_url, $imageDomain),
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            // 'is_copyright' => $this->is_copyright,
            'sub_docquyen' => (bool) $this->sub_docquyen,
            // 'trailer_url' => $this->trailer_url,
            'time' => $this->time,
            'episode_current' => $this->episode_current,
            // 'episode_total' => $this->episode_total,
            'quality' => $this->quality,
            'lang' => $this->lang,
            // 'notify' => $this->notify,
            // 'showtimes' => $this->showtimes,
            // 'view' => $this->view,
            // 'actor' => json_decode($this->actor),
            // 'director' => json_decode($this->director),
            'category' => $this->formattedCategoriesArray('category'),
            // 'country' => $this->formattedArray('country'),
        ];
    }

    protected function formatImageUrl($url, $domain)
    {
        return $url ? $domain . $url : null;
    }

    protected function formattedArray($propertyName)
    {
        $propertyValue = $this->$propertyName;

        return $propertyValue !== null
            ? array_map(function ($item) {
                return ['name' => $item['name'], 'slug' => $item['slug']];
            }, json_decode($propertyValue, true))
            : null;
    }

    protected function formattedCategoriesArray($propertyName)
    {
        $propertyValue = $this->$propertyName;
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
    
        return  array_map(function ($item) use ($categories) {
                foreach ($categories as $category_slug => $category_name) {
                    if ($item['slug'] == $category_slug) {
                        return ['name' => $category_name];
                    }
                }
            }, json_decode($propertyValue, true));
    }

}

