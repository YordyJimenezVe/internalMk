<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class ImageHelper
{
    /**
     * Compress an uploaded image file and save it as a JPEG.
     * If the GD library is not available or fails, it falls back to raw storage.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $disk
     * @param int $quality
     * @return string
     */
    public static function compressAndStore(UploadedFile $file, string $folder, string $disk = 'public', int $quality = 60): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getClientMimeType();

        // Target name
        $fileName = uniqid() . '.jpg';
        $targetPath = storage_path('app/' . $disk . '/' . $folder);

        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $targetFile = $targetPath . '/' . $fileName;
        $image = false;

        try {
            if (function_exists('imagecreatefromjpeg') && (in_array($extension, ['jpg', 'jpeg']) || str_contains($mime, 'jpeg'))) {
                $image = @imagecreatefromjpeg($file->getRealPath());
            } elseif (function_exists('imagecreatefrompng') && ($extension === 'png' || str_contains($mime, 'png'))) {
                $image = @imagecreatefrompng($file->getRealPath());
                if ($image) {
                    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                    $white = imagecolorallocate($bg, 255, 255, 255);
                    imagefill($bg, 0, 0, $white);
                    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                    imagedestroy($image);
                    $image = $bg;
                }
            } elseif (function_exists('imagecreatefromgif') && ($extension === 'gif' || str_contains($mime, 'gif'))) {
                $image = @imagecreatefromgif($file->getRealPath());
            } elseif (function_exists('imagecreatefromwebp') && ($extension === 'webp' || str_contains($mime, 'webp'))) {
                $image = @imagecreatefromwebp($file->getRealPath());
            }

            if ($image && function_exists('imagejpeg')) {
                $success = @imagejpeg($image, $targetFile, $quality);
                imagedestroy($image);
                if ($success) {
                    return $folder . '/' . $fileName;
                }
            }
        } catch (\Throwable $e) {
            // Ignore error and fall back to raw upload
        }

        // Fallback to storing original file
        return $file->store($folder, $disk);
    }
}
