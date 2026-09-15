<?php

declare(strict_types=1);

namespace App\Campus\Import;

use DOMNode;
use RuntimeException;

class SyllabusParser
{
    /** @return list<array{course_code: string, name: string, url: string, section_id: string}> */
    public function listing(string $body, string $url, int $year): array
    {
        $html    = new Html($body);
        $this->year($html, $year);
        $entries = [];
        foreach ($html->nodes('//a[@href]') as $node) {
            $target           = OfficialUrl::resolve($url, Html::attr($node, 'href'));
            if (! preg_match('~/'.$year.'_[12]_[JE]_\d+\.html#([A-Za-z0-9_-]+)$~', $target, $fragment)
                || ! preg_match('/^([A-Z]+\d+[A-Z0-9-]*)\s+(.+)$/u', Html::text($node), $match)) {
                continue;
            }
            OfficialUrl::assertAllowed($target);
            $entries[$target] = ['course_code' => $match[1], 'name' => $match[2], 'url' => $target, 'section_id' => $fragment[1]];
        }
        if (! $entries) {
            throw new RuntimeException('No 2026 syllabus links found: '.$url);
        }

        return array_values($entries);
    }

    private function year(Html $html, int $year): void
    {
        $title = Html::text($html->first('//title'));
        if (! preg_match('/\b'.$year.'\b/u', $title) && ! str_contains($title, $year.'年度')) {
            throw new RuntimeException('Syllabus year could not be verified: '.$title);
        }
    }

    /** @param array{course_code: string, name: string, url: string, section_id: string} $entry
     * @return array<string, mixed>
     */
    public function course(string $body, array $entry, int $year): array
    {
        $html     = new Html($body);
        $this->year($html, $year);
        $section  = $html->first('//*[@id="'.$entry['section_id'].'"]');
        if (! $section) {
            throw new RuntimeException('Missing syllabus section '.$entry['section_id']);
        }
        $title    = Html::text($html->first('./ul/li/a', $section));
        if (! preg_match('/^'.preg_quote($entry['course_code'], '/').'\s+(.+)$/u', $title, $name)) {
            throw new RuntimeException('Course code/title disagrees with index: '.$entry['url']);
        }
        $fields   = [];
        $teachers = [];
        foreach ($html->nodes('.//tr[th and td]', $section) as $row) {
            // Official HTML has unclosed tables: DOMDocument can nest later subjects.
            // Only rows whose nearest syllabus container is this subject are eligible.
            $owner = $html->first('ancestor::div[contains(concat(" ", normalize-space(@class), " "), " sytab ")][1]', $row);
            if ($owner !== null && Html::attr($owner, 'id') !== $entry['section_id']) {
                continue;
            }
            $label = Html::text($html->first('./th', $row));
            $cell  = $html->first('./td', $row);
            if ($cell === null) {
                continue;
            }
            foreach (['semester' => 'Semester', 'credits' => 'Credits', 'description' => 'Course outline', 'day' => 'Day of', 'period' => 'Period', 'classroom' => 'Classroom', 'teaching_mode' => '授業形態'] as $key => $needle) {
                if (stripos($label, $needle) !== false) {
                    $fields[$key] = trim(Html::lines($cell));
                }
            }
            foreach (['instructor' => 'Instructor', 'coordinator' => 'Coordinator', 'responsible' => 'Responsible'] as $role => $needle) {
                if (stripos($label, $needle) !== false) {
                    array_push($teachers, ...$this->teachers($html, $cell, $role, $entry['url']));
                }
            }
        }
        if (! isset($fields['semester']) || ! str_contains($fields['semester'], (string) $year)) {
            throw new RuntimeException('Course semester/year missing or mismatched: '.$entry['url']);
        }

        return $fields + [
            'academic_year' => $year, 'course_code' => $entry['course_code'], 'name' => $name[1],
            'section_id'    => $entry['section_id'], 'syllabus_url' => $entry['url'], 'teachers' => $teachers,
        ];
    }

    /** @return list<array{name: string, external_teacher_id: ?string, role: string}> */
    private function teachers(Html $html, DOMNode $cell, string $role, string $base): array
    {
        $ids    = [];
        foreach ($html->nodes('.//a[@href]', $cell) as $link) {
            $target = OfficialUrl::resolve($base, Html::attr($link, 'href'));
            try {
                OfficialUrl::assertAllowed($target);
                $ids[NameMatcher::compact(Html::text($link))] = OfficialUrl::teacherId($target);
            } catch (RuntimeException) {
                // External links are never used as official IDs.
            }
        }
        $result = [];
        foreach (preg_split('/[,、;；\r\n]+/u', Html::lines($cell)) ?: [] as $raw) {
            $name     = trim((string) preg_replace('/[\s\x{3000}]+/u', ' ', $raw));
            if ($name === '' || in_array($name, ['－', '-', '未定', 'TBA'], true)) {
                continue;
            }
            $result[] = ['name' => $name, 'external_teacher_id' => $ids[NameMatcher::compact($name)] ?? null, 'role' => $role];
        }

        return $result;
    }
}
