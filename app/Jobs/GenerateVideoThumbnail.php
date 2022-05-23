<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Pawlox\VideoThumbnail\VideoThumbnail;

class GenerateVideoThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public string $video_path;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($video_path)
    {
        $this->video_path = $video_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $storageUrl = '/path/to/thumbnail/';
        $fileName = basename($this->video_path).'_thumbnail';
        VideoThumbnail::createThumbnail(
            $this->video_path,
            $storageUrl,
            $fileName,
            0,
            $width = 640,
            $height = 480
        );
    }
}
