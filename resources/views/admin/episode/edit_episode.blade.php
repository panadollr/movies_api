<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$movieDetail->name}}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.css" integrity="sha512-KXol4x3sVoO+8ZsWPFI/r5KBVB/ssCGB5tsv2nVOKwLg33wTFP3fmnXa47FdSVIshVTgsYk/1734xSk9aFIa4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
  body{
    background-color: rgb(17 24 39);
  }
</style>
  </head>

<body>
<h1 class="ui inverted center aligned header" style="margin-top: 10px;">Chỉnh sửa tập phim : {{$movieDetail->name}} ({{count($ophimEpisodesV2)}} tập)</h1>
<div class="ui container">
<form class="ui inverted form" action="{{ url('admin/episodes/update/'. $movieDetail->_id) }}" method="POST">
@foreach($ophimEpisodesV2 as $episode)
<div style="border: 2px solid white; margin-top: 10px; padding: 10px">
  <div class="field">
    <h4 class="ui inverted header">Link_m3u8 {{$episode['slug']}}: <span style="color:#21ba45">{{$episode['link_m3u8']}}</span></h4>
  </div>
  <div class="field">
  <div class="ui labeled input">
  <div class="ui blue label">
  Link abyss {{$episode['slug']}}
  </div>
  <input name="server_2[{{$episode['slug']}}]" type="text" value="{{$episode['server_2']}}" >
</div>
  </div>
  </div>
  @endforeach
  <br>
  <center><button class="ui button" type="submit">Cập nhật</button></center>
  <br>
</form>
</div>

</body>
</html>
