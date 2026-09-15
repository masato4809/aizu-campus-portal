<?php

declare(strict_types=1);

namespace App\Pipe;

use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Trait\GraphQLVariablesHelper;
use Illuminate\Support\MessageBag;

abstract class PipeBase
{
    use GraphQLVariablesHelper;

    protected EPipeKind $kind     = EPipeKind::INVALID;

    /** @var array<mixed> */
    protected array $caller       = [];

    protected ?MessageBag $errors = null;

    abstract protected function parse(): void;

    abstract public function validate(): void;

    /**
     * PipeBase constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(EPipeKind $kind, array $args)
    {
        $this->kind = $kind;
        $this->args = $args;

        // パラメータをエラートレース用に保持しておく.
        PipeService::storePipe($kind, $args);

        $this->parse();
    }

    /**
     * パイプ種別を取得.
     */
    public function getKind(): EPipeKind
    {
        return $this->kind;
    }

    /**
     * 発生エラー一覧を取得.
     *
     * @return array<mixed>
     */
    public function getValidationErrors(): array
    {
        if ($this->errors === null) {
            return [];
        }

        return $this->errors->messages();
    }

    /**
     * 特定クラスからコール済みか.
     */
    public function isCalled(string $class): bool
    {
        return in_array($class, $this->caller, true);
    }

    /**
     * バリデーションが正常か.
     */
    public function isValid(): bool
    {
        if ($this->errors === null) {
            return true;
        }

        return $this->errors->count() === 0;
    }

    /**
     * アップデート実行を記録.
     */
    public function onUpdate(string $class): void
    {
        $this->caller[] = $class;
    }
}
