<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$movieDetail->name}}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.css" integrity="sha512-KXol4x3sVoO+8ZsWPFI/r5KBVB/ssCGB5tsv2nVOKwLg33wTFP3fmnXa47FdSVIshVTgsYk/1734xSk9aFIa4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<style>
  body{
    background-color: rgb(17 24 39);
  }
</style>

<body>
<div class="ui container">
    <br>
    <h1 class="ui inverted center aligned header">Chỉnh sửa tập phim trên của phim : {{$movieDetail->name}}</h1>
<form class="ui inverted form">
@foreach($ophimEpisodesV2 as $t)
  <div class="field">
    <label>First Name</label>
    <input type="text" name="first-name" placeholder="First Name">
    <label>First Name</label>
    <input type="text" name="first-name" placeholder="First Name">
  </div>
  @endforeach
  <button class="ui button" type="submit">Submit</button>
</form>
</div>

</body>
</html>
