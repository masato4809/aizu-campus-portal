<?php

declare(strict_types=1);

namespace App\Campus\Import;

class NameMatcher
{
    public static function normalize(string $name): string
    {
        $name = mb_convert_kana($name, 'asKV', 'UTF-8');
        $name = mb_strtolower($name);

        return trim((string) preg_replace('/[\s\p{P}]+/u', ' ', $name));
    }

    public static function compact(string $name): string
    {
        return str_replace(' ', '', self::normalize($name));
    }

    /** @return list<string> */
    public static function reordered(string $name): array
    {
        $parts  = explode(' ', self::normalize($name));
        $orders = [];
        // Rotate at each possible family/given boundary, preserving multiword names.
        for ($boundary = 1; $boundary < count($parts); $boundary++) {
            $orders[] = implode(' ', array_merge(array_slice($parts, $boundary), array_slice($parts, 0, $boundary)));
        }

        return $orders;
    }

    /** @param list<array<string, mixed>> $teachers
     * @return array{teacher_id: ?int, method: string}
     */
    public function match(string $name, ?string $externalId, array $teachers): array
    {
        if ($externalId !== null) {
            $ids = array_values(array_filter($teachers, fn ($t) => $t['external_teacher_id'] === $externalId));

            return count($ids) === 1 ? ['teacher_id' => (int) $ids[0]['id'], 'method' => 'official_id'] : ['teacher_id' => null, 'method' => 'unknown_or_ambiguous_id'];
        }
        foreach (['english_exact', 'english_reordered', 'english_normalized', 'japanese_or_alias'] as $method) {
            $found = [];
            foreach ($teachers as $teacher) {
                $en      = $teacher['name_en'] ?? '';
                $matches = match ($method) {
                    'english_exact'      => $en !== '' && $name                === $en,
                    'english_reordered'  => $en !== '' && in_array(self::normalize($name), self::reordered($en), true),
                    'english_normalized' => $en !== '' && self::compact($name) === self::compact($en),
                    default              => in_array(self::compact($name), array_map(self::compact(...), array_merge([$teacher['name']], $teacher['aliases'] ?? [])), true),
                };
                if ($matches && self::compact($name) !== '') {
                    $found[(int) $teacher['id']] = true;
                }
            }
            if (count($found) > 1) {
                return ['teacher_id' => null, 'method' => 'ambiguous_'.$method];
            }
            if (count($found) === 1) {
                return ['teacher_id' => array_key_first($found), 'method' => $method];
            }
        }

        return ['teacher_id' => null, 'method' => 'unmatched'];
    }
}
