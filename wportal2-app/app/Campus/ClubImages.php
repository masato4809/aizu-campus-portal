<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClubImages
{
    /** @return array{path: string, mime_type: string, width: int, height: int} */
    public function store(UploadedFile $file, string $field): array
    {
        $info    = @getimagesize($file->getPathname());
        $formats = [IMAGETYPE_JPEG => 'jpeg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
        if (! $info || ! isset($formats[$info[2]]) || $info[0] * $info[1] > 16000000) {
            throw ValidationException::withMessages([$field => '画像はJPEG・PNG・WebP形式、1,600万画素以下で選んでください。']);
        }

        // The production image pipeline uses GD for resizing. Keep uploads usable
        // in lightweight Docker images where the optional GD extension is absent.
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagecreatetruecolor')) {
            $format = $formats[$info[2]];
            $path   = Str::uuid().'.'.$format;
            $bytes  = $file->getContent();
            try {
                if (! Storage::disk('campus_clubs')->put($path, $bytes)) {
                    throw new \RuntimeException('Could not store club image.');
                }
            } catch (\Throwable $exception) {
                $this->delete([$path]);
                throw $exception;
            }

            return ['path' => $path, 'mime_type' => 'image/'.$format, 'width' => $info[0], 'height' => $info[1]];
        }

        $source  = @imagecreatefromstring($file->getContent());
        if (! $source) {
            throw ValidationException::withMessages([$field => '画像を読み込めませんでした。別の画像を選んでください。']);
        }

        $scale   = min(1, 1600 / max($info[0], $info[1]));
        $width   = max(1, (int) round($info[0] * $scale));
        $height  = max(1, (int) round($info[1] * $scale));
        $target  = imagecreatetruecolor($width, $height);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
        $format  = $formats[$info[2]];
        $path    = Str::uuid().'.'.$format;
        ob_start();
        try {
            $encoded = match ($format) {
                'jpeg' => imagejpeg($target, null, 85),
                'png'  => imagepng($target, null, 6),
                'webp' => imagewebp($target, null, 85),
            };
            $bytes   = ob_get_contents();
        } finally {
            ob_end_clean();
            imagedestroy($source);
            imagedestroy($target);
        }
        if (! $encoded || ! is_string($bytes) || $bytes === '') {
            throw ValidationException::withMessages([$field => '画像を変換できませんでした。別の画像を選んでください。']);
        }
        try {
            if (! Storage::disk('campus_clubs')->put($path, $bytes)) {
                throw new \RuntimeException('Could not store club image.');
            }
        } catch (\Throwable $exception) {
            $this->delete([$path]);
            throw $exception;
        }

        return ['path' => $path, 'mime_type' => 'image/'.$format, 'width' => $width, 'height' => $height];
    }

    /** @param list<string> $paths */
    public function delete(array $paths): void
    {
        if ($paths === []) {
            return;
        }
        // Report cleanup failures without masking the original persistence error.
        try {
            if (! Storage::disk('campus_clubs')->delete($paths)) {
                throw new \RuntimeException('Could not remove club images.');
            }
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
