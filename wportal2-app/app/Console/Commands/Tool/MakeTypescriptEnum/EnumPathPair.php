<?php

declare(strict_types=1);

namespace App\Console\Commands\Tool\MakeTypescriptEnum;

use Illuminate\Support\Str;

class EnumPathPair
{
    public string $inputPath    = '';

    public string $outputPath   = '';

    private string $enumExplain = '';

    private string $enumName    = '';

    private string $enumType    = '';

    /** @var EnumValue[] */
    private array $enumList     = [];

    /**
     * 入力ファイルが存在するか.
     */
    public function isExistInputFile(): bool
    {
        return file_exists($this->inputPath);
    }

    /**
     * 出力ファイルが存在するか.
     */
    public function isExistOutputFile(): bool
    {
        return file_exists($this->outputPath);
    }

    /**
     * Enum全体の説明を取得.
     */
    public function getEnumExplain(): string
    {
        return $this->enumExplain;
    }

    /**
     * Enum名をUpperSnakeで取得.
     */
    public function getEnumNameUpperSnake(): string
    {
        return Str::upper(Str::snake($this->enumName));
    }

    /**
     * Enum名をUpperCamelで取得.
     */
    public function getEnumNameUpperCamel(): string
    {
        return Str::studly($this->enumName);
    }

    /**
     * Enumの型を取得.
     */
    public function getEnumType(): string
    {
        return $this->enumType;
    }

    /**
     * Enumの配列を取得.
     *
     * @return EnumValue[]
     */
    public function getEnumList(): array
    {
        return $this->enumList;
    }

    /**
     * 入力パラメータを取得.
     *
     * @return array{bool, string}
     */
    public function parseInputParameter(): array
    {
        if (! $this->isExistInputFile()) {
            return [false, '対象ファイルなし'];
        }

        // ファイルの内容を取得.
        $fileContents   = file_get_contents($this->inputPath);
        if (! $fileContents) {
            return [false, '対象ファイルなし'];
        }

        // 正規表現で改行なしデータからenum説明を取得.
        $regex          = preg_match(
            '/(\/\*.*?\*\/)/s',
            $fileContents,
            $matches
        );
        if ($regex) {
            $this->enumExplain = $matches[1];
        }

        // 正規表現でenum名を取得.
        $regex          = preg_match('/enum (.*):(.*)/', $fileContents, $matches);
        if (! $regex) {
            return [false, 'enum名不正'];
        }
        $this->enumName = trim($matches[1]);
        $this->enumType = trim($matches[2]);

        // 正規表現で改行なしデータから定義データ群を取得.
        $regex          = preg_match(
            '/enum .*?:.*?{\n(.*)\n}/s',
            $fileContents,
            $matches
        );
        if (! $regex) {
            return [false, 'enumブロックに不正'];
        }
        $enums          = explode("\n", $matches[1]);
        foreach ($enums as $enum) {
            // 空行などを除外.
            $regex            = preg_match('/\scase\s/', $enum);
            if (! $regex) {
                continue;
            }
            /*
            if (! str_contains($enum, 'case')) {
                continue;
            }
            */

            $regex            = preg_match('/case(.*)=(.*);.*\/\/(.*)/', $enum, $matches);
            if (! $regex) {
                return [false, "enum値・コメント不正 -> $enum"];
            }
            $key              = trim($matches[1]);
            $value            = trim($matches[2]);
            $comment          = trim($matches[3]);

            // 重複キー.
            if (isset($this->enumList[$key])) {
                return [false, 'enum値・重複'];
            }

            // enum配列に格納.
            $enum             = new EnumValue($key, $value, $comment);
            $this->enumList[] = $enum;
        }

        return [true, ''];
    }

    /**
     * typeスクリプトのブロックとして文字列を出力する.
     */
    public function toTypescriptBlock(): string
    {
        $upperSnake = $this->getEnumNameUpperSnake();
        $upperCamel = $this->getEnumNameUpperCamel();

        $block      = '';
        if (! empty($this->enumExplain)) {
            $block .= $this->getEnumExplain()."\n";
        }
        $block .= "export const $upperSnake = {\n";
        foreach ($this->enumList as $enum) {
            $block .= "  $enum->key: $enum->value, // $enum->comment\n";
        }
        $block .= "} as const;\n";
        $block .= "export type $upperCamel = typeof ${upperSnake}[keyof typeof $upperSnake];";

        return $block;
    }
}
