<?php

declare(strict_types=1);

namespace App\Services\Internal;

use App\Enum\App\EPipeKind;
use App\Enum\App\EStatusCode;
use App\Pipe\PipeBase;
use Exception;

/**
 * 各Serviceを実行する際のpipe管理サービス.
 */
class PipeService
{
    /** @var PipeBase[] */
    protected array $pipes             = [];

    /** @var array<mixed> */
    protected array $pipeArgs          = [];

    /**
     * エラートレース用.
     */
    /** @var array<mixed> */
    protected array $paramArgs         = [];

    protected EPipeKind $pipeKind      = EPipeKind::INVALID;

    protected EStatusCode $statusCode  = EStatusCode::OK;

    protected string $statusMessage    = '';

    /**
     * @return PipeBase[]
     */
    public function getPipes(string $class): array
    {
        return array_filter($this->pipes, function ($pipe) use ($class) {
            return ! $pipe->isCalled($class);
        });
    }

    /**
     * @throws Exception
     */
    public function throwResult(EStatusCode $code, Exception $e): void
    {
        $this->statusCode    = $code;
        $this->statusMessage = $e->getMessage();
        throw $e;
    }

    /**
     * 実行結果を返す.
     *
     * @param  array<mixed>  $attachResult
     * @return array<mixed>
     */
    public function getResult(array $attachResult = []): array
    {
        // pipeで所持しているstatusCodeで処理.
        return $this->getResultWithStatus(
            $this->statusCode,
            $this->statusMessage,
            $attachResult
        );
    }

    /**
     * ステータスを指定して実行結果を返す.
     *
     * @param  array<mixed>  $attachResult
     * @return array<mixed>
     */
    public function getResultWithStatus(EStatusCode $code, string $message = '', array $attachResult = []): array
    {
        // 基本出力.
        $resultArray = [
            'statusCode'    => $code->value,
            'statusMessage' => $message,
        ];

        // 追加で指定されたresultを設定する.
        foreach ($attachResult as $key => $value) {
            $resultArray[$key] = $value;
        }

        // 実行結果として返す.
        return $resultArray;
    }

    /**
     * バリデーション実行結果を返す.
     *
     * @param  array<mixed>  $attachResult
     * @return array<mixed>
     */
    public function getValidationResult(array $attachResult = []): array
    {
        $statusCode     = EStatusCode::UNPROCESSABLE_ENTITY->value;
        $statusMessage  = 'validation error';

        // バリデーションエラーを格納.
        $errorValues    = [];
        foreach ($this->pipes as $pipe) {
            $validationErrors = $pipe->getValidationErrors();
            foreach ($validationErrors as $key => $validationError) {
                foreach ((array) $validationError as $message) {
                    $errorValues[$key][] = $message;
                }
            }
        }

        $errors         = json_encode($errorValues);

        return [
            ...compact('statusCode', 'errors', 'statusMessage'),
            ...$attachResult,
        ];
    }

    /**
     * バリデーションエラーがあるかどうか.
     */
    public function isValid(): bool
    {
        foreach ($this->pipes as $pipe) {
            if (! $pipe->isValid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * 新しいパイプの追加.
     */
    public function insertNewPipe(PipeBase $pipe): PipeBase
    {
        $this->pipes[] = $pipe;
        $pipe->validate();

        return $pipe;
    }

    /**
     * フロントから渡されたパラメータを取得する.
     *
     * @return array<mixed>
     */
    public function getParamArgs(): array
    {
        return $this->paramArgs;
    }

    /**
     * フロントから渡されたパラメータを取得する.
     *
     * @return array{EPipeKind, array<mixed>}
     */
    public function getPipeArgs(): array
    {
        return [$this->pipeKind, $this->pipeArgs];
    }

    /**
     * フロントから渡されたパラメータをエラートレース用に保持しておく.
     *
     * @param  array<mixed>  $args
     */
    public function storeParam(array $args): void
    {
        $this->paramArgs = $args;
    }

    /**
     * フロントから渡されたパラメータをエラートレース用に保持しておく.
     *
     * @param  array<mixed>  $args
     */
    public function storePipe(EPipeKind $kind, array $args): void
    {
        $this->pipeKind = $kind;
        $this->pipeArgs = $args;
    }
}
