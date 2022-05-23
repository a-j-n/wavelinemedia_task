<?php

namespace App\Helpers;

use App\Jobs\GenerateImageThumbnail;
use App\Jobs\GenerateVideoThumbnail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class S3MediaHelper
{
    public static function upload($file)
    {
        if (is_string($file)) {
            $file = new File($file);
        }

        $path = self::saveToS3($file);

        $mimetype = $file->getMimeType(); // example video/mp4 || image/jpg

        if (Str::contains($mimetype, 'video')) {
            GenerateVideoThumbnail::dispatch($path);
        } elseif (Str::contains($mimetype, 'image')) {
            GenerateImageThumbnail::dispatch($path);
        }


    }

    public function saveToS3($file)
    {
        return Storage::disk('s3')->putFile('photos', $file);
    }


}
