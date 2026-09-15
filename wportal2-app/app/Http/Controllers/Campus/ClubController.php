<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\ClubCatalog;
use App\Campus\ClubImages;
use App\Campus\CurrentStudent;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClubController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $me      = app(CurrentStudent::class)->requireStudent($request);
        $data    = $request->validate([
            'name'        => [
                'required',
                'string',
                'max:120',
                Rule::exists('campus_club_catalog', 'name')->where(fn ($query) => $query->where('university', $me->university)),
            ],
            'body'        => 'required|string|min:10|max:2000',
            'contact_url' => 'nullable|url:http,https|max:2048',
            'website_url' => 'nullable|url:http,https|max:2048',
            'images'      => 'nullable|array|max:4',
            'images.*'    => 'required|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'images.max'     => '画像は1投稿につき4枚までです。',
            'images.*.max'   => '画像は1枚5MB以下にしてください。',
            'images.*.image' => 'JPEG・PNG・WebPの画像を選んでください。',
            'images.*.mimes' => 'JPEG・PNG・WebPの画像を選んでください。',
        ]);
        unset($data['images']);
        $images  = [];
        $storage = app(ClubImages::class);
        try {
            foreach ($request->file('images', []) as $index => $file) {
                $images[] = $storage->store($file, 'images.'.$index);
            }
            DB::transaction(function () use ($data, $me, $images) {
                $id = DB::table('campus_clubs')->insertGetId($data + ['student_id' => $me->id, 'created_at' => now(), 'updated_at' => now()]);
                foreach ($images as $image) {
                    DB::table('campus_club_images')->insert($image + ['club_id' => $id]);
                }
            });
        } catch (\Throwable $exception) {
            $storage->delete(array_column($images, 'path'));
            throw $exception;
        }

        return redirect('/campus/clubs')->with('status', 'サークル情報を投稿しました。');
    }

    public function updateCatalog(Request $request, int $catalog): RedirectResponse
    {
        $me                 = app(CurrentStudent::class)->requireStudent($request);
        $data               = $request->validate([
            'category'    => 'required|string|max:40',
            'tags'        => 'nullable|string|max:255',
            'contact_url' => 'nullable|url:http,https|max:2048',
            'website_url' => 'nullable|url:http,https|max:2048',
            'password'    => 'required|string|min:10|max:128',
            'image'       => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        abort_unless(app(ClubCatalog::class)->findForUniversity($catalog, $me->university) !== null, 404);
        $configuredPassword = (string) config('campus.club_edit_password', '');
        if ($configuredPassword === '' || ! hash_equals($configuredPassword, $data['password'])) {
            throw ValidationException::withMessages(['password' => '編集用パスワードが違います。']);
        }

        $splitTags          = preg_split(
            '/[,，、\\n]+/u',
            (string) ($data['tags'] ?? ''),
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        if ($splitTags === false) {
            $splitTags = [];
        }

        $tags               = collect($splitTags)
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $storage            = app(ClubImages::class);
        $newImage           = null;
        $oldPath            = null;
        try {
            if ($request->hasFile('image')) {
                $newImage = $storage->store($request->file('image'), 'image');
            }
            $oldPath = DB::transaction(function () use ($catalog, $me, $data, $tags, $newImage): ?string {
                $record = DB::table('campus_club_catalog')
                    ->where('id', $catalog)
                    ->where('university', $me->university)
                    ->lockForUpdate()
                    ->first();
                abort_unless($record !== null, 404);

                $update = [
                    'category'    => $data['category'],
                    'tags'        => json_encode($tags, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'contact_url' => $data['contact_url'] ?? null,
                    'website_url' => $data['website_url'] ?? null,
                    'updated_at'  => now(),
                ];
                if ($newImage !== null) {
                    $update += [
                        'image_path'      => $newImage['path'],
                        'image_mime_type' => $newImage['mime_type'],
                        'image_width'     => $newImage['width'],
                        'image_height'    => $newImage['height'],
                    ];
                }
                DB::table('campus_club_catalog')->where('id', $catalog)->update($update);

                return $record->image_path ?? null;
            });
        } catch (\Throwable $exception) {
            if ($newImage !== null) {
                $storage->delete([$newImage['path']]);
            }
            throw $exception;
        }
        if ($newImage !== null && $oldPath !== null) {
            $storage->delete([$oldPath]);
        }

        return redirect('/campus/clubs')->with('status', 'サークル情報を更新しました。');
    }

    public function image(Request $request, int $image): StreamedResponse
    {
        $me     = app(CurrentStudent::class)->requireStudent($request);
        $record = DB::table('campus_club_images as i')
            ->join('campus_clubs as c', 'c.id', '=', 'i.club_id')
            ->join('campus_students as s', 's.id', '=', 'c.student_id')
            ->where('i.id', $image)->where('s.university', $me->university)->select('i.*')->first();
        abort_unless($record !== null, 404);
        $disk   = Storage::disk('campus_clubs');
        abort_unless($disk->exists($record->path), 404);

        return $disk->response($record->path, null, [
            'Content-Type'           => $record->mime_type,
            'Cache-Control'          => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function catalogImage(Request $request, int $catalog): StreamedResponse
    {
        $me     = app(CurrentStudent::class)->requireStudent($request);
        $record = DB::table('campus_club_catalog')
            ->where('id', $catalog)
            ->where('university', $me->university)
            ->first();
        abort_unless($record !== null && filled($record->image_path), 404);
        $disk   = Storage::disk('campus_clubs');
        abort_unless($disk->exists($record->image_path), 404);

        return $disk->response($record->image_path, null, [
            'Content-Type'           => $record->image_mime_type,
            'Cache-Control'          => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy(Request $request, int $club): RedirectResponse
    {
        $me    = app(CurrentStudent::class)->requireStudent($request);
        $paths = DB::transaction(function () use ($me, $club) {
            $post  = DB::table('campus_clubs')->where('id', $club)->where('student_id', $me->id)->lockForUpdate()->first();
            abort_unless($post !== null, 404);
            $paths = DB::table('campus_club_images')->where('club_id', $club)->pluck('path')
                ->map(fn ($path): string => (string) $path)->values()->all();
            DB::table('campus_clubs')->where('id', $club)->delete();

            return array_values($paths);
        });
        app(ClubImages::class)->delete($paths);

        return redirect('/campus/clubs')->with('status', 'サークルの投稿と添付画像を削除しました。');
    }
}
