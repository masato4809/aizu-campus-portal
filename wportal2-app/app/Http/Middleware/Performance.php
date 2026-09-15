<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Performance
{
    private bool $isActive             = false;

    /**
     * 処理時間.
     */
    private float $timeStart           = 0;

    private float $timeEnd             = 0;

    private string $timeDuring         = '';

    /**
     * メモリ情報.
     */
    private int $memoryInitial         = 0;

    private string $memoryInitialLabel = '';

    private int $memoryMax             = 0;

    private string $memoryMaxLabel     = '';

    private string $memoryUseLabel     = '';

    /**
     * DB情報.
     */
    /** @var Collection<int, array<mixed>>|null */
    private ?Collection $queryList     = null;

    private int $queryCount            = 0;

    private float $queryTime           = 0.0;

    /** @var Collection<int, mixed>|null */
    private ?Collection $querySqlList  = null;

    /**
     * フラグが有効の場合に計測を実施する.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // 計測開始.
        $this->startMeasure($request);

        $request = $next($request);

        // 計測終了.
        $this->endMeasure();

        return $request;
    }

    /**
     * 計測の開始.
     */
    private function startMeasure(Request $request): void
    {
        // TODO: imageの対応が終わるまで暫定除外.
        $imageUrl                 = \Safe\preg_match('/image/', $request->url());
        if ($imageUrl) {
            return;
        }

        // コンフィグ有効の場合のみ.
        if (! config('app.performance_measure')) {
            return;
        }

        $this->isActive           = true;

        // 開始時間.
        $this->timeStart          = microtime(true);

        // 初期メモリ.
        $this->memoryInitial      = memory_get_usage();
        $this->memoryInitialLabel = number_format($this->memoryInitial / (1024 * 1024), 3).'MB';

        // DB計測.
        $this->queryList          = new Collection;
        DB::listen(function ($query) {
            if (is_null($this->queryList)) {
                return;
            }

            // クエリ時間の抽出.
            $queryTime = $query->time;
            $sql       = $query->sql;

            // 取得した情報を保存.
            $this->queryList->add([
                'queryTime' => $queryTime,
                'sql'       => $sql,
            ]);
        });
    }

    /**
     * 計測の終了.
     */
    private function endMeasure(): void
    {
        if (! $this->isActive || $this->queryList === null) {
            return;
        }

        // 終了時間.
        $this->timeEnd        = microtime(true);

        // 経過時間.
        $this->timeDuring     = number_format($this->timeEnd - $this->timeStart, 3).'[s]';

        // 最大メモリ.
        $this->memoryMax      = memory_get_peak_usage();
        $this->memoryMaxLabel = number_format($this->memoryMax / (1024 * 1024), 3).'MB';

        // 推定利用メモリ.
        $this->memoryUseLabel = number_format(($this->memoryMax - $this->memoryInitial) / (1024*1024), 3).'MB';

        // クエリ回数.
        $this->queryCount     = $this->queryList->count();

        // クエリ時間.
        $this->queryTime      = (float) $this->queryList->sum(
            fn ($query) => (float) $query['queryTime']
        );

        // クエリSQL.
        $this->querySqlList   = $this->queryList->map(
            fn ($query) => $query['sql']
        );

        // 結果の出力.
        $this->printResult();
    }

    /**
     * 結果の表示.
     */
    private function printResult(): void
    {
        if ($this->querySqlList === null) {
            return;
        }

        $sql    = $this->querySqlList->map(function ($sql) {
            $pos = strpos((string) $sql, 'where');

            return $pos ? substr((string) $sql, 0, (int) strpos((string) $sql, 'where')).'...' : $sql;
        })->map(function ($sql) {
            $pos = strpos((string) $sql, 'values');

            return $pos ? substr((string) $sql, 0, (int) strpos((string) $sql, 'values')).'...' : $sql;
        })->map(function ($sql) {
            $pos = strpos((string) $sql, 'set');

            return $pos ? substr((string) $sql, 0, (int) strpos((string) $sql, 'set')).'...' : $sql;
        })
            ->implode("\n");

        $result = <<<EOF
### パフォーマンス計測 ###
経過時間: $this->timeDuring
-------------------
利用メモリ：$this->memoryUseLabel
(初期:$this->memoryInitialLabel / 最大:$this->memoryMaxLabel)
-------------------
クエリ回数:$this->queryCount
クエリ時間:$this->queryTime[ms]
$sql
#######################
EOF;

        Log::info("\n".$result);
    }
}
