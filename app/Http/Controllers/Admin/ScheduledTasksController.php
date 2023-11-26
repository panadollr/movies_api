<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduledTasksController extends Controller
{

    public function run_scheduled_tasks()
    {
         try {
            \Artisan::call('schedule:run');
            return "Scheduled tasks executed successfully.";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function run_scheduled_tasks2()
{
          // Gọi API
      $response = $this->client->request('GET', 'https://api.themoviedb.org/3/trending/movie/day?api_key=' . $this->api_key);
  
      // Chuyển đổi dữ liệu từ API
      $data = json_decode($response->getBody());
  
      // Kiểm tra và cập nhật dữ liệu mới
      foreach ($data->results as $result) {
          // Kiểm tra xem dữ liệu đã tồn tại trong cơ sở dữ liệu chưa
          $existingMovie = TrendingMovie::where('id', $result->id)->first();
  
          if (!$existingMovie) {
              // Nếu dữ liệu chưa tồn tại, tạo một bản ghi mới
              $newMovie = new TrendingMovie();
              $newMovie->id = $result->id;
              $newMovie->title = $result->title;
              $newMovie->backdrop_path = $result->backdrop_path;
              $newMovie->	overview = $result->overview;
              $newMovie->poster_path = $result->poster_path;
              $newMovie->release_date = $result->release_date;
              $newMovie->vote_average = $result->vote_average;
              $newMovie->vote_count = $result->vote_count;
              $newMovie->popularity = $result->popularity;
              $newMovie->save();
          }
      }
}
}
