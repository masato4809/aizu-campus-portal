<?php

declare(strict_types=1);

namespace App\Enum\ValidationRules;

use App\Enum\App\EValidationType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * バリデーション情報の整形を実行.
 */
final class ValidationPacker
{
    /** @var Collection<int, ValidationPackerData> */
    private Collection $dataList;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->dataList = new Collection;
    }

    /**
     * バリデーション指定の追加.
     *
     * @param  Collection<int, ValidationRuleSet>  $ruleSetList
     */
    public function addValidation(
        string $tag,
        string $columnName,
        mixed $value,
        Collection $ruleSetList,
    ): void {
        // タグが重複している場合は登録処理を行わない.
        if ($this->dataList->contains(function (ValidationPackerData $data) use ($tag) {
            return $data->tag === $tag;
        })) {
            Log::error("duplicate validation tag $tag");

            return;
        }

        // データをひとまとめに保存.
        $this->dataList->add(new ValidationPackerData(
            $tag,
            $columnName,
            $value,
            $ruleSetList
        ));
    }

    /**
     * バリデーション用のデータ配列を取得.
     *
     * @return array<string, mixed|null>
     */
    public function getData(): array
    {
        return $this->dataList->flatMap(function (ValidationPackerData $data) {
            return [$data->tag => $data->value];
        })->toArray();
    }

    /**
     * バリデーション用のルール配列を取得.
     *
     * @return array<mixed>
     */
    public function getRules(): array
    {
        return $this->dataList->flatMap(function (ValidationPackerData $data) {
            return EValidationType::getRules(
                $data->tag,
                $data->ruleSetList
            );
        })->toArray();
    }

    /**
     * バリデーション用のメッセージ配列を取得.
     *
     * @return array<mixed>
     */
    public function getMessages(): array
    {
        return $this->dataList->flatMap(function (ValidationPackerData $data) {
            return EValidationType::getMessages(
                $data->tag,
                $data->columnName,
                $data->ruleSetList
            );
        })->toArray();
    }

    /**
     * validatorの生成.
     */
    public function makeValidator(): \Illuminate\Validation\Validator
    {
        return Validator::make(
            $this->getData(),
            $this->getRules(),
            $this->getMessages()
        );
    }
}
