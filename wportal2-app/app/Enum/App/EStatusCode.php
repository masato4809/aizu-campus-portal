<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * APIステータスコード.
 * vendor/symfony/http-foundation/Response.phpを利用すればよいが
 * その場合はフロントとステータス定義を同期しづらいため.
 */
enum EStatusCode: int
{
    case INVALID              = 0;   // 無効.

    // 200-.
    case OK                   = 200;      // リクエストが正常に処理できた

    // 400-.
    case BAD_REQUEST          = 400; // サーバーがそのリクエストを処理しない (できない).
    case UNAUTHORIZED         = 401; // アクセス権が無い、または認証に失敗.
    case NOT_FOUND            = 404; // Webページが見つからない.
    case GONE                 = 410; // 利用不可のリソースへのアクセス.
    case UNPROCESSABLE_ENTITY = 422; // リクエストは適正だが意味が異なるためサーバが返すことが出来ない

    // 500-.
}
