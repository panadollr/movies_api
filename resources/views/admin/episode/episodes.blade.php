<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Episode CMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.css" integrity="sha512-KXol4x3sVoO+8ZsWPFI/r5KBVB/ssCGB5tsv2nVOKwLg33wTFP3fmnXa47FdSVIshVTgsYk/1734xSk9aFIa4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<style>
  body{
    background-color: rgb(17 24 39);
  }
</style>

<body>

<div style="padding: 10px;">
<table class="ui celled table">
<h2 class="ui center aligned header" style="margin-top: 5px; color: white">Chỉnh sửa tập phim</h2>

<center>
<div class="ui stackable three item menu"  style="width: 80%;">
<a href="{{ url('admin/episodes') }}" class="item {{ empty(request()->all()) ? 'active' : '' }}">Tất cả</a>
<div class="ui simple dropdown item" >
    Lọc theo loại phim
    <i class="dropdown icon"></i>
    <div class="menu">
      <a href="{{ url('admin/episodes') }}" class="item {{ empty(request()->all()) ? 'active' : '' }}">Tất cả</a>
      <a href="{{ url('admin/episodes?type=series') }}" class="item {{ request('type') == 'series' ? 'active' : '' }}">Phim bộ</a>
      <a href="{{ url('admin/episodes?type=single') }}" class="item {{ request('type') == 'single' ? 'active' : '' }}">Phim lẻ</a>
    </div>
  </div>

<a class="item"><form action="{{ url('admin/episodes') }}" method="GET">  
<div class="ui action input">
  <input style="border: 2px solid black;" type="text" name="searchTerm" placeholder="Tìm kiếm phim theo slug hoặc tên">
  <button class="ui icon black button">
    <i class="search icon"></i>
  </button>
</div>
</form></a>
</div>

<div class="ui stackable borderless menu">
@php
    $alphabets = ['A', 'Ă', 'Â', 'B', 'C' ,'D', 'Đ',
                  'E', 'Ê', 'G', 'H', 'I', 'K', 'L', 'M',
                  'N', 'O', 'Ô', 'Ơ', 'P', 'Q', 'R',
                  'S', 'T', 'U', 'Ư', 'V', 'X', 'Y'];
    @endphp

    <div class="header item">Lọc theo bảng chữ cái</div>
    @foreach($alphabets as $alphabet)
    <a href="{{ url('admin/episodes?type=alphabet&alphabet=' . $alphabet) }}" class="item {{ request('alphabet') == $alphabet ? 'active' : '' }}">{{ $alphabet }}</a>
  @endforeach
</div>

</center>

  <thead>
    <tr><th>Tên phim</th>
    <th>Ảnh</th>
    <th >slug</th>
    <th >Loại phim</th>
    <th >Thời lượng</th>
    <th>Số tập phim đã có trên server 2 (abyss)</th>
    <th class="three wide">Tùy chỉnh</th>
  </tr></thead>
  <tbody>
    @if(request()->input('slug'))
      <h3 style="color:#21ba45">Kết quả tìm kiếm cho slug : {{request()->input('slug')}}</h3>
    @endif
  @foreach($movies as $movie)
    <tr>
      <td>
      {{$movie->name}}
      </td>
      <td>
      @php 
      $file_name = $movie->poster_url;
      $new_file_name = str_replace(".jpg", ".webp", $file_name);
      @endphp
      <img src="https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_70/uploads/movies/{{$new_file_name}}" alt="" srcset="">
      </td>
      <td>
      {{$movie->slug}}
      </td>
      <td>
      @if($movie->type == 'single')
      Phim lẻ
      @else 
        Phim bộ
      @endif
      </td>
      <td>
        {{$movie->time}}
      </td>
      <td>
      @php
        $episodesServer2Count = collect($episodes)->where('_id', $movie->_id)->where('server_2', '!=', '')->count();
    @endphp
    
      @php
      $string = str($movie->episode_current);
      if (preg_match('/\((\d+)\/\d+\)/', $string, $matches)) {
          $number = $matches[1];
      } elseif (preg_match('/\d+/', $string, $matches)) {
          $number = $matches[0];
      } else if($string == "Full"){
        $number = 1;
      }
      else {
          $number = null;
      }
      @endphp
    {{$episodesServer2Count}} / {{$number}}
    <span style="margin-left: 10px;"></span>
    @if($episodesServer2Count == $number )
    <div class="ui green label">Đã hoàn thành</div>
    @else
    <div class="ui black label">Chưa hoàn thành</div>
    @endif
      </td>
      <td>
        <center>
      <a href="{{ url('admin/episodes/edit/'. $movie->slug) }}" class="ui blue button">Chỉnh sửa Server 2 (abyss)</a>
      </center>
      </td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>

  <tr><th colspan="6">
      <div class="ui right floated pagination menu">
        @if ($movies->currentPage() > 1)
          <a href="{{ $movies->previousPageUrl() }}" class="item">
            <i class="left chevron icon"></i>
          </a>
        @endif

        @php
          $length = 5;
          $endPage = min($movies->currentPage() + $length - 1, $movies->lastPage());
        @endphp

        @for ($i = $movies->currentPage(); $i <= $endPage; $i++)
    @if ($i == $endPage && $i != $movies->lastPage())
        <a class="item">...</a>
        <a href="{{ $movies->url($movies->lastPage()) }}" class="item{{ $movies->currentPage() == $i ? ' active' : '' }}">
            {{ $movies->lastPage() }}
        </a>
    @elseif ($i == $movies->lastPage())
        <a href="{{ $movies->url($i) }}" class="item{{ $movies->currentPage() == $i ? ' active' : '' }}">
            {{ $movies->lastPage() }}
        </a>
    @else
        <a href="{{ $movies->url($i) }}" class="item{{ $movies->currentPage() == $i ? ' active' : '' }}">
            {{ $i }}
        </a>
    @endif
@endfor
        
        @if ($movies->hasMorePages())
          <a href="{{ $movies->nextPageUrl() }}" class="item">
            <i class="right chevron icon"></i>
          </a>
        @endif
      </div>
    </th>
  </tr>
</tfoot>
</table>

</div>

</body>
</html>
