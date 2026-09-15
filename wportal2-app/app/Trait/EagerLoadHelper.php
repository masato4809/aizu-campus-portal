<?php

declare(strict_types=1);

namespace App\Trait;

trait EagerLoadHelper
{
    /**
     * eager-load指定に特定のキーが存在するかどうか.
     *
     * @param  array<mixed>  $with
     * @param  array<string>  $history
     */
    protected function isExistKeyInWith(array $with, string $key, array $history = []): bool
    {
        // .連結の場合の特定のキー検索.
        // .連結した値がキーか値に存在する.
        $withKey = count($history)
            ? implode('.', $history).'.'.$key
            : $key;
        if (array_key_exists($withKey, $with) || in_array($withKey, $with, true)) {
            return true;
        }

        // array指定のwith対策.
        // Historyの分だけarrayを探索し、そのarrayのキーかバリューにある.
        if (count($history) !== 0) {
            $withArray = $this->historyToArray($with, $history);
            if (array_key_exists($key, $withArray) || in_array($key, $withArray, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * historyをarrayに変換.
     *
     * @param  array<mixed>  $with
     * @param  array<string>  $history
     * @return array<mixed>
     */
    private function historyToArray(array $with, array $history): array
    {
        $withArray = $with;
        foreach ($history as $historyKey) {
            $withArray = $withArray[$historyKey] ?? null;
            if (! is_array($withArray)) {
                return [];
            }
        }

        return $withArray;
    }
}
