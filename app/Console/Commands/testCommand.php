<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class testCommand extends Command
{
    protected $signature = 'command:name';
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inputImagePath = 'https://img.ophim9.cc/uploads/movies/chang-quy-cua-toi-poster.jpg';

        // Đường dẫn đến nơi lưu hình ảnh nén
        $outputImagePath = 'path/to/your/compressed/image.png';

        // Tạo một đối tượng hình ảnh từ file PNG
        $image = imagecreatefrompng($inputImagePath);

        // Nén hình ảnh (compression level: 0-9, 0 là nén cao nhất)
        imagepng($image, $outputImagePath, 9);

        // Giải phóng bộ nhớ của hình ảnh
        imagedestroy($image);
    }
}
