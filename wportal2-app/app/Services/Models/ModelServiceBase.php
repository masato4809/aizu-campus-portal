<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\App\Auth\AuthUser;
use App\Models\App\ModelBase;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ModelServiceBase
{
    /**
     * 新規登録（orFail）
     *
     * @param ModelBase<covariant Model>|AuthUser $model
     *
     * @throws Throwable
     */
    public function insertOrFail(ModelBase|AuthUser $model): void
    {
        if (! empty($model->id)) {
            throw new Exception('invalid primary key.');
        }
        $model->saveOrFail();
    }

    /**
     * 更新処理（orFail)
     *
     * @param ModelBase<covariant Model>|AuthUser $model
     *
     * @throws Exception|Throwable
     */
    public function updateOrFail(ModelBase|AuthUser $model): void
    {
        if (empty($model->id)) {
            throw new Exception('invalid primary key.');
        }
        $model->saveOrFail();
    }

    /**
     * 削除処理（orFail）
     *
     * @param ModelBase<covariant Model>|AuthUser $model
     *
     * @throws Exception|Throwable
     */
    public function deleteOrFail(ModelBase|AuthUser $model): void
    {
        if (empty($model->id)) {
            throw new Exception('invalid primary key.');
        }
        $model->deleteOrFail();
    }
}
