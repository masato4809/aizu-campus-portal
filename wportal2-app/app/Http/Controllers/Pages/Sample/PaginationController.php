<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Sample;

use App\Facades\Models\App\Trn\TrnUserService;
use App\Http\Controllers\Controller;
use App\Models\App\Trn\TrnUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Response;

class PaginationController extends Controller
{
    public function invoke(Request $request): Response
    {
        return $this->render('Sample/Pagination/Index', [
            'paginationTrnUser'  => fn () => $this->getPaginationFacility($request),
            'fixList'            => fn () => $this->getFixList(),
        ]);
    }

    /**
     * ページネーション・施設
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     */
    private function getPaginationFacility(Request $request, array $with = []): array
    {
        // パラメータ受け取り.
        $pageIndex = (int) $request->input('index', 0);
        $pageStep  = (int) $request->input('step', 3);

        return TrnUserService::offsetPagination(
            $pageIndex,
            $pageStep,
            $with
        );
    }

    /**
     * 固定リスト・施設.
     *
     * @param  array<mixed>  $with
     * @return array<mixed>
     */
    private function getFixList(array $with = []): array
    {
        return TrnUser::query()
            ->with($with)
            ->limit(5)
            ->get()
            ->map(function (Model $trnUser) use ($with) {
                /** @var TrnUser $trnUser */
                return $trnUser->toPayload($with);
            })->toArray();
    }
}
