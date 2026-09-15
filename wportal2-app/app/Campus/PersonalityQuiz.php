<?php

declare(strict_types=1);

namespace App\Campus;

class PersonalityQuiz
{
    /** @var list<array{axis:string,text:string,a:string,b:string}> */
    public const QUESTIONS   = [
        ['axis' => 'activity', 'text' => '休日の過ごし方は？', 'a' => '外に出かけてアクティブに過ごす', 'b' => '家でゆっくり過ごす'],
        ['axis' => 'activity', 'text' => 'サークルやイベントの誘いがあったら？', 'a' => '予定を空けてでも参加したい', 'b' => '気が向いたら参加する程度でいい'],
        ['axis' => 'activity', 'text' => '忙しいスケジュールについてどう思う？', 'a' => 'やることが多いくらいが張り合いがある', 'b' => '余裕のあるスケジュールが好き'],
        ['axis' => 'group_size', 'text' => '理想の集まりは？', 'a' => '大人数でワイワイ盛り上がる場', 'b' => '少人数でじっくり話せる場'],
        ['axis' => 'group_size', 'text' => '新しい環境に入ったら？', 'a' => 'できるだけ多くの人と知り合いたい', 'b' => '気の合う数人と仲良くなりたい'],
        ['axis' => 'group_size', 'text' => '相談したいことがあるとき？', 'a' => 'みんなで意見を出し合いたい', 'b' => '信頼できる一人にじっくり相談したい'],
        ['axis' => 'challenge', 'text' => 'サークル選びで重視するのは？', 'a' => '今までやったことのない新しい活動', 'b' => '慣れ親しんだ活動の延長'],
        ['axis' => 'challenge', 'text' => '知らない場所に誘われたら？', 'a' => '面白そうだからとりあえず行ってみる', 'b' => '勝手が分かってから行きたい'],
        ['axis' => 'challenge', 'text' => '大学生活で大事にしたいのは？', 'a' => '新しい発見や刺激', 'b' => '安心できる居場所'],
    ];

    private const AXIS_ORDER = ['activity', 'group_size', 'challenge'];

    /** @param array<int,string> $answers 1始まりの設問番号 => 'a'|'b' */
    public function score(array $answers): string
    {
        $tally = array_fill_keys(self::AXIS_ORDER, ['a' => 0, 'b' => 0]);
        foreach (self::QUESTIONS as $i => $question) {
            $answer = $answers[$i + 1] ?? 'a';
            $tally[$question['axis']][$answer]++;
        }

        $code  = '';
        foreach (self::AXIS_ORDER as $axis) {
            $code .= $tally[$axis]['a'] >= $tally[$axis]['b'] ? 'A' : 'B';
        }

        return $code;
    }
}
