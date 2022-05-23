<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class GenerateImageThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $image_path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($image_path)
    {
        $this->image_path = $image_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->createThumbnail(60, 60);
        $this->createThumbnail(120, 120);
    }

    public function createThumbnail($width, $height)
    {
        $img = Image::make($this->image_path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $file_name = $width . '_' . $height . basename($this->image_path);
        $file_path = '/path/to/thumbnail/';
        Storage::disk('s3')->putFileAs($file_path, $img, $file_name);
    }
}
