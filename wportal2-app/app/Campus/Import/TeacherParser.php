<?php

declare(strict_types=1);

namespace App\Campus\Import;

use RuntimeException;

class TeacherParser
{
    /** @return array<string, array{url: string, alias: string}> */
    public function listing(string $body, string $url): array
    {
        $html   = new Html($body);
        $result = [];
        foreach ($html->nodes('//a[@href]') as $link) {
            $target = OfficialUrl::resolve($url, Html::attr($link, 'href'));
            $id     = OfficialUrl::teacherId($target);
            if ($id !== null) {
                OfficialUrl::assertAllowed($target);
                // Prefer the plain name in list items over department-head decorations.
                $alias       = Html::text($html->first('.//*[contains(@class,"faculty_name") and not(contains(@class,"_en"))]', $link));
                $result[$id] = ['url' => 'https://u-aizu.ac.jp/research/faculty/detail?cd='.$id, 'alias' => $alias ?: Html::text($link)];
            }
        }
        if (! $result) {
            throw new RuntimeException('No faculty profile links found');
        }

        return $result;
    }

    /** @return array<string, mixed> */
    public function profile(string $body, string $url): array
    {
        $html = new Html($body);
        $root = $html->first('//*[@id="faculty-detail"]');
        if (! $root) {
            throw new RuntimeException('Faculty profile container missing: '.$url);
        }
        $name = Html::text($html->first('.//h2[contains(@class,"faculty_name")]/span[1]', $root));
        $en   = Html::text($html->first('.//*[contains(@class,"faculty_name_en")]', $root));
        if ($name === '') {
            throw new RuntimeException('Faculty name missing: '.$url);
        }
        $data = ['external_teacher_id' => OfficialUrl::teacherId($url), 'name' => $name, 'name_en' => $en ?: null, 'profile_url' => $url];
        foreach (['email', 'department', 'position', 'research_field', 'laboratory_name', 'laboratory_url', 'personal_url'] as $field) {
            $data[$field] = null;
        }
        foreach ($html->nodes('.//dl[dt and dd]', $root) as $dl) {
            $label = Html::text($html->first('./dt', $dl));
            $cell  = $html->first('./dd', $dl);
            if (! $cell) {
                continue;
            }
            $value = trim(Html::lines($cell));
            $key   = match ($label) {
                '所属', 'Affiliation'        => 'department', '職位', 'Position' => 'position',
                '研究分野', 'Research Field' => 'research_field', '研究室', '研究室名', 'Laboratory' => 'laboratory_name',
                default                      => null,
            };
            if ($key !== null) {
                $data[$key] = $value ?: null;
            }
            if (preg_match('/^(E-?Mail|メールアドレス)$/i', $label)) {
                // Scope to the profile's email field, never the university footer.
                $address       = Html::attr($html->first('.//a[starts-with(@href,"mailto:")]', $cell) ?? $cell, 'href');
                $candidate     = $address !== '' ? explode('?', substr($address, 7))[0] : $value;
                $data['email'] = filter_var($candidate, FILTER_VALIDATE_EMAIL) ? $candidate : null;
            }
            if (in_array($label, ['Webサイト', 'Website', '個人ページ', '研究室URL'], true)) {
                $href = Html::attr($html->first('.//a[@href]', $cell) ?? $cell, 'href');
                if ($href !== '') {
                    $target = OfficialUrl::resolve($url, $href);
                    if (in_array(parse_url($target, PHP_URL_SCHEME), ['https', 'http'], true)) {
                        $data[$label === '研究室URL' ? 'laboratory_url' : 'personal_url'] = $target;
                    }
                }
            }
        }

        return $data;
    }
}
