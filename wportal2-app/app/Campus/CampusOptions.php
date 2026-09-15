<?php

declare(strict_types=1);

namespace App\Campus;

class CampusOptions
{
    public const UNIVERSITY         = '会津大学';

    public const RATINGS            = [
        'credit_ease'          => '単位の取りやすさ',
        'grade_ease'           => '高成績の取りやすさ',
        'assignment_lightness' => '課題の少なさ',
        'satisfaction'         => '満足度',
        'past_exam_similarity' => '過去問の出題傾向',
    ];

    public const USER_TYPES         = ['cost_performance' => 'コスパタイプ', 'gpa' => 'GPA必要タイプ', 'learning' => '学問をしっかりしたいタイプ'];

    public const EVALUATION_METHODS = ['test' => 'テスト', 'report' => 'レポート', 'attendance' => '出席'];

    public const LEVELS             = ['many' => '多め', 'few' => '少なめ', 'none' => 'なし'];

    public const CLASS_SIZES        = ['under_20' => '20人未満', 'range_20_39' => '20〜39人', 'over_40' => '40人以上'];

    public const SEMESTERS          = ['first' => '前期', 'second' => '後期'];

    public const DEPARTMENTS        = ['コンピュータ理工学部' => ['コンピュータ理工学科']];

    public const TYPES              = ['AAA', 'AAB', 'ABA', 'ABB', 'BAA', 'BAB', 'BBA', 'BBB'];

    public const TYPE_LABELS        = [
        'AAA' => '太陽タイプ（ムードメーカー）',
        'AAB' => 'リーダータイプ',
        'ABA' => '冒険家タイプ',
        'ABB' => '職人タイプ',
        'BAA' => '世話焼きタイプ',
        'BAB' => '縁の下の力持ちタイプ',
        'BBA' => 'マイペース挑戦者タイプ',
        'BBB' => '癒し系タイプ',
    ];

    public const GOAL_ORIENTATIONS  = [
        'high_grade' => '成績',
        'min_effort' => '単位取得',
        'academic'   => '学びの深さ',
        'balanced'   => '特にこだわらない',
    ];
}
