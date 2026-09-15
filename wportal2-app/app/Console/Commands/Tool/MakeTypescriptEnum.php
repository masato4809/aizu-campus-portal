<?php

declare(strict_types=1);

namespace App\Console\Commands\Tool;

use App\Console\Commands\Tool\MakeTypescriptEnum\EnumPathPair;
use Illuminate\Console\Command;

use function in_array;

class MakeTypescriptEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'tool:MakeTypescriptEnum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'phpに定義されているenumをtypescriptに出力する';

    /**
     * New a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('EnumのTypescriptへの出力を開始');

        // 入力ファイルと出力ファイルのペアの取得
        $pathPairList = $this->getPathPairList();

        // 各ファイルの処理.
        foreach ($pathPairList as $pathPair) {
            if (! $this->exportTypescript($pathPair)) {
                return self::FAILURE;
            }
        }

        $this->info('EnumのTypescriptへの出力を終了');

        return self::SUCCESS;
    }

    /**
     * 出力無視対象.
     */
    const IGNORE_LIST      = [
        'EPipeKind',
    ];

    /**
     * 出力無視ディレクトリ.
     */
    const IGNORE_DIR_LIST  = [
        '',
    ];

    /**
     * enumの入力・出力のペアを取得する.
     *
     * @return EnumPathPair[]
     */
    private function getPathPairList(): array
    {
        $ret        = [];

        // ディレクトリ直下から巡回取得
        $inputFiles = glob(app_path().'/Enum/E*.php');
        if ($inputFiles === false) {
            return $ret;
        }
        foreach ($inputFiles as $inputFile) {
            $fileName             = basename($inputFile, '.php');
            if (in_array($fileName, self::IGNORE_LIST, true)) {
                $this->info("${fileName}を除外");

                continue;
            }

            $pathPair             = new EnumPathPair;
            $pathPair->inputPath  = $inputFile;
            $pathPair->outputPath = resource_path().'/script/Enum/Server/'.$fileName.'.ts';
            $ret[]                = $pathPair;
        }

        // サブディレクトリから巡回
        $this->info('サブディレクトリへの処理を実施');
        $inputDirs  = glob(app_path().'/Enum/*', GLOB_ONLYDIR);
        if ($inputDirs === false) {
            return $ret;
        }
        foreach ($inputDirs as $inputDir) {
            $distDir    = str_replace(app_path().'/Enum/', '', $inputDir);
            if (in_array($distDir, self::IGNORE_DIR_LIST, true)) {
                $this->info("{$distDir}を除外");

                continue;
            }

            // 再帰的に取得する.
            $this->getPathPairRecursive($inputDir, $distDir, $ret);
        }

        return $ret;
    }

    /**
     * 指定ディレクトリから対象ファイルを取得
     *
     * @param  EnumPathPair[]  $ret
     */
    private function getPathPairRecursive(string $inputDir, string $distDir, array &$ret): void
    {
        // 直下から巡回取得.
        $inputFiles = glob($inputDir.'/E*.php');
        if ($inputFiles === false) {
            return;
        }
        foreach ($inputFiles as $inputFile) {
            $fileName             = basename($inputFile, '.php');
            if (in_array($fileName, self::IGNORE_LIST, true)) {
                $this->info("{$fileName}を除外");

                continue;
            }

            $pathPair             = new EnumPathPair;
            $pathPair->inputPath  = $inputFile;
            $pathPair->outputPath = resource_path()."/script/Enum/Server/$distDir/$fileName.ts";
            $ret[]                = $pathPair;
        }

        // サブディレクトリがあればさらに巡回.
        $subDirs    = glob("$inputDir/*", GLOB_ONLYDIR);
        if ($subDirs === false) {
            return;
        }
        foreach ($subDirs as $subDir) {
            $distDir    = str_replace(app_path().'/Enum/', '', $subDir);
            if (in_array($distDir, self::IGNORE_DIR_LIST, true)) {
                $this->info("{$distDir}を除外");

                continue;
            }

            // 再帰的に取得する.
            $this->getPathPairRecursive($subDir, $distDir, $ret);
        }
    }

    /**
     * 指定ペアのtypescript出力を実施.
     */
    private function exportTypescript(EnumPathPair $pathPair): bool
    {
        $this->info('個別出力処理');

        // 基本パラメータの取得.
        [$success, $reason] = $pathPair->parseInputParameter();
        if (! $success) {
            $this->error('パラメータの取得に失敗:'.$pathPair->inputPath." > $reason");

            return false;
        }

        // 出力ファイル存在チェック.
        if ($pathPair->isExistOutputFile()) {
            // 更新の場合.
            if (! $this->updateTypescript($pathPair)) {
                $this->error('更新に失敗:'.$pathPair->inputPath);

                return false;
            }
        } else {
            // 新規作成の場合
            if (! $this->createTypescript($pathPair)) {
                $this->error('新規作成に失敗:'.$pathPair->inputPath);

                return false;
            }
        }

        return true;
    }

    /**
     * 新規作成処理.
     */
    private function createTypescript(EnumPathPair $pathPair): bool
    {
        $this->info(' >新規作成を開始:'.$pathPair->outputPath);

        $typescriptBlock = $pathPair->toTypescriptBlock();
        $newCode         = "\n\n// #ENUM_DEFINE_START#\n$typescriptBlock\n// #ENUM_DEFINE_END#";
        file_put_contents($pathPair->outputPath, $newCode);

        $this->info(' >新規作成を終了:'.$pathPair->outputPath);

        return true;
    }

    /**
     * 更新処理.
     */
    private function updateTypescript(EnumPathPair $pathPair): bool
    {
        $this->info('> 更新を開始:'.$pathPair->outputPath);

        $existCode       = file_get_contents($pathPair->outputPath);
        if (! $existCode) {
            return false;
        }

        $typescriptBlock = $pathPair->toTypescriptBlock();
        $newCode         = preg_replace(
            '/\/\/ #ENUM_DEFINE_START#.*\/\/ #ENUM_DEFINE_END#/s',
            "// #ENUM_DEFINE_START#\n$typescriptBlock\n// #ENUM_DEFINE_END#",
            $existCode
        );
        file_put_contents($pathPair->outputPath, $newCode);

        $this->info('> 更新を終了:'.$pathPair->outputPath);

        return true;
    }
}
