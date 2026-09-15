<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * 指定したS3の画像をページの一部として利用可能な形式で返す.
     */
    public function invoke(Request $request): object
    {
        // URIからパスを取得.
        $path     = (string) $request->route('path');

        // S3への参照を取得.
        $storage  = Storage::disk('storage');

        // ファイルが存在するかチェック.
        if (! $storage->exists($path)) {
            abort(404);
        }

        // S3から対象ファイルとファイル情報を取得する.
        $file     = $storage->get($path);
        $fileName = basename($path);
        $mineType = $storage->mimeType($path);

        // inlineとして返す.
        return response($file)
            ->withHeaders([
                'Content-disposition'           => 'inline; filename='.$fileName,
                'Access-Control-Expose-Headers' => 'Content-Disposition',
                'Content-Type'                  => $mineType,
            ]);
    }
}
