<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movie CMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.css" integrity="sha512-KXol4x3sVoO+8ZsWPFI/r5KBVB/ssCGB5tsv2nVOKwLg33wTFP3fmnXa47FdSVIshVTgsYk/1734xSk9aFIa4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<style>
  body{
    background-color: rgb(17 24 39);
  }
</style>

<body>
  

<div style="padding: 10px;">
<table class="ui celled padded table">
<h2 class="ui center aligned header" style="margin-top: 20px; color: white">Chỉnh sửa tập phim phim</h2>

<center>
<form action="{{ url('admin/movies/edit') }}" method="GET">  
<div class="ui action input">
  <input type="text" name="slug" placeholder="Tìm kiếm phim theo slug">
  <button class="ui icon button">
    <i class="search icon"></i>
  </button>
</div>
</form>
</center>

  <thead>
    <tr><th>Tên phim</th>
    <th >slug</th>
    <th >Loại phim</th>
    <th>Số tập phim đã có trên server 2 (abyss)</th>
    <th>Số tập phim đã có trên server 3 (ok.ru)</th>
    <th class="five wide">Tùy chỉnh</th>
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
      {{$movie->slug}}
      </td>
      <td>
      @if($movie->type == 'series')
        Phim bộ
      @else 
        Phim lẻ
      @endif
      </td>
      <td>
      @php
        $episodesServer3Count = 0;
      @endphp
      
      @foreach($episodes as $episode)
        @if($movie->_id == $episode->_id)
          @if($episode->server_3 != '')
            @php
                $episodesServer3Count++;
            @endphp
          @endif
        @endif
      @endforeach

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
    {{$episodesServer3Count}} / {{$number}}
      </td>
      <td>
      @php
        $episodesServer3Count = 0;
      @endphp
      
      @foreach($episodes as $episode)
        @if($movie->_id == $episode->_id)
          @if($episode->server_3 != '')
            @php
                $episodesServer3Count++;
            @endphp
          @endif
        @endif
      @endforeach

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
    {{$episodesServer3Count}} / {{$number}}
      </td>
      <td>
      <button class="ui black button">Server 2 (abyss)</button>
      <button class="ui blue button">Server 3 (ok.ru)</button>
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
