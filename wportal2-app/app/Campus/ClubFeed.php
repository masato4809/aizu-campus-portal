<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClubFeed
{
    /** @return Collection<int, \stdClass> */
    public function get(\stdClass $me, string $tab = 'reviews'): Collection
    {
        $catalog       = app(ClubCatalog::class)->all($me->university)->keyBy('name');
        $posts         = DB::table('campus_clubs as c')->join('campus_students as s', 's.id', '=', 'c.student_id')
            ->where('s.university', $me->university)->select('c.*', 's.name as author')
            ->orderByDesc('c.created_at')->orderByDesc('c.id')->get();
        $images        = DB::table('campus_club_images')->whereIn('club_id', $posts->pluck('id'))
            ->orderBy('id')->get()->groupBy('club_id');
        foreach ($posts as $post) {
            $post->images = $images->get($post->id, collect());
        }
        $postsByName   = $posts->groupBy('name');

        // Aggregate members and reviews separately: joining both would multiply counts.
        $members       = DB::table('campus_students')->where('university', $me->university)
            ->where('circle', '<>', '')->select('circle')->selectRaw('COUNT(*) as member_count')
            ->groupBy('circle')->get()->keyBy('circle');
        $personalities = DB::table('campus_students')->where('university', $me->university)
            ->where('circle', '<>', '')->whereIn('personality', CampusOptions::TYPES)
            ->select('circle', 'personality')->selectRaw('COUNT(*) as total')
            ->groupBy('circle', 'personality')->get()->groupBy('circle');
        $reviews       = DB::table('campus_reviews as r')
            ->join('campus_students as s', 's.id', '=', 'r.student_id')
            ->join('campus_courses as c', 'c.id', '=', 'r.course_id')
            ->where('s.university', $me->university)->where('c.university', $me->university)
            ->where('s.circle', '<>', '')->select('s.circle')
            ->selectRaw('COUNT(*) as review_count, MAX(r.created_at) as latest_review_at')
            ->groupBy('s.circle')->get()->keyBy('circle');

        /** @var Collection<int, \stdClass> $clubs */
        $clubs         = $catalog->keys()->merge($members->keys())->merge($postsByName->keys())
            ->filter(fn (string $name): bool => $catalog->has($name))
            ->unique()->map(function ($name) use ($me, $members, $personalities, $reviews, $postsByName, $catalog): \stdClass {
                $types     = $personalities->get($name, collect())->sortByDesc('total');
                $diagnosed = (int) $types->sum('total');
                $matching  = (int) ($types->firstWhere('personality', $me->personality)->total ?? 0);
                $official  = $catalog->get($name);

                if ($official === null) {
                    throw new \RuntimeException('Club catalog entry not found: '.(string) $name);
                }

                return (object) [
                    'catalog_id'              => (int) $official->id,
                    'name'                    => (string) $name,
                    'category'                => (string) $official->category,
                    'tags'                    => $official->tags,
                    'contact_url'             => $official->contact_url,
                    'website_url'             => $official->website_url,
                    'source_url'              => $official->source_url,
                    'catalog_image_url'       => filled($official->image_path ?? null) ? '/campus/club-catalog-images/'.$official->id : null,
                    'catalog_image_mime_type' => $official->image_mime_type      ?? null,
                    'catalog_image_width'     => $official->image_width             ?? null,
                    'catalog_image_height'    => $official->image_height            ?? null,
                    'member_count'            => (int) ($members->get($name)->member_count ?? 0),
                    'review_count'            => (int) ($reviews->get($name)->review_count ?? 0),
                    'latest_review_at'        => $reviews->get($name)->latest_review_at ?? '',
                    'diagnosed_count'         => $diagnosed,
                    'matching_count'          => $matching,
                    'similarity'              => $diagnosed && $me->personality ? $matching / $diagnosed * 100 : null,
                    'distribution'            => $types->map(fn ($type) => (object) [
                        'type'       => $type->personality, 'count' => (int) $type->total,
                        'percentage' => round($type->total / $diagnosed * 100, 1),
                    ])->values(),
                    'posts'                   => $postsByName->get($name, collect()),
                ];
            });

        return $clubs->sort(function ($a, $b) use ($tab) {
            if ($tab === 'similarity') {
                $difference = ($b->similarity ?? -1) <=> ($a->similarity ?? -1);
                if ($difference !== 0) {
                    return $difference;
                }
            }

            return ($b->review_count <=> $a->review_count)
                ?: strcmp($b->latest_review_at, $a->latest_review_at)
                ?: strcmp($a->name, $b->name);
        })->values();
    }
}
