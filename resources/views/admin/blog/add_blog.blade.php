<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title></title>

    @extends('libraries')
</head>
<body >
    <br>
    <div class="ui container">
    <h2 class="ui center aligned header">Thêm blog mới</h2>
        <form class="ui form" action="{{ url('admin/blogs/create') }}" method="post">
        <div class="field">
    <label>Tiêu đề</label>
    <input namespace="Tiêu đề" type="text" name="title" value="">
  </div>
  <div class="field">
    <label>poster_url</label>
    <input namespace="poster_url" type="text" name="poster_url" value="">
  </div>
  <div class="field">
    <label>Thể loại phim</label>
    <input namespace="Thể loại" type="text" name="movie_type" value="">
  </div>
  <div class="field">
    <label>Nội dung</label>
    <div id="editor">
        <textarea name="content" id="blog-textarea" cols="30" rows="10"></textarea>
    </div>
  </div>
  <center><button class="ui fluid black button" type="submit">Thêm mới</button></center>
</form>
    </div>
    
<br>
<br>
    
@extends('admin.blog.ckeditor-js')
</body>
</html>
