<?php

declare(strict_types=1);

namespace App;

class Constant
{
    // 不正SPログインとみなす回数.
    const int INVALID_SP_LOGIN_COUNT          = 10;

    // イメージ画像のディレクトリパス.
    const string FACE_IMAGE_S3_PATH           = 'personal_setting/face/';

    // デフォルトのイメージ画像.
    const string FACE_IMAGE_DEFAULT           = 'image/face_default_%02d.png';

    // デフォルトのイメージ画像のインデックスの最小値.
    const int FACE_IMAGE_DEFAULT_INDEX_MIN    = 1;

    // デフォルトのイメージ画像のインデックスの最大値.
    const int FACE_IMAGE_DEFAULT_INDEX_MAX    = 14;
}
