<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClubCatalog
{
    public const SOURCE_URL = 'https://web-ext.u-aizu.ac.jp/circles/c-jichi/members/';

    /** @return Collection<int, \stdClass> */
    public function all(string $university = CampusOptions::UNIVERSITY): Collection
    {
        return DB::table('campus_club_catalog')
            ->where('university', $university)
            ->orderBy('name')
            ->get()
            ->map(function (\stdClass $club): \stdClass {
                $club->tags = $this->decodeTags($club->tags);

                return $club;
            })
            ->values();
    }

    public function findForUniversity(int $id, string $university): ?\stdClass
    {
        $club = DB::table('campus_club_catalog')
            ->where('id', $id)
            ->where('university', $university)
            ->first();

        if ($club !== null) {
            $club->tags = $this->decodeTags($club->tags);
        }

        return $club;
    }

    /** @return list<string> */
    public function decodeTags(?string $tags): array
    {
        $decoded = json_decode((string) $tags, true);

        return is_array($decoded)
            ? array_values(array_filter($decoded, 'is_string'))
            : [];
    }
}
