<?php

declare(strict_types=1);

namespace App\Http\Requests\Campus;

use App\Campus\CampusOptions;
use App\Campus\CurrentStudent;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app(CurrentStudent::class)->get($this) !== null;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $total = (int) $this->input('test_weight', 0) + (int) $this->input('report_weight', 0) + (int) $this->input('attendance_weight', 0);
            if ($total > 100) {
                $validator->errors()->add('test_weight', '評価方式の割合の合計は100%以内にしてください。');
            } elseif ($total === 0) {
                $validator->errors()->add('test_weight', '評価方式の割合を少なくとも1つ入力してください。');
            }
        });
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        $departments = CampusOptions::DEPARTMENTS[(string) $this->input('faculty')] ?? [];
        $rules       = [
            'course_id'            => 'nullable|integer|min:1',
            'course'               => 'required_without:course_id|nullable|string|max:255',
            'professor'            => 'required_without:course_id|nullable|string|max:1000',
            'faculty'              => 'nullable|string|in:'.implode(',', array_keys(CampusOptions::DEPARTMENTS)),
            'department'           => 'nullable|string|in:'.implode(',', $departments),
            'user_type'            => 'required|string|in:'.implode(',', array_keys(CampusOptions::USER_TYPES)),
            'has_past_exam'        => 'required|boolean',
            'test_weight'          => 'nullable|integer|between:0,100',
            'report_weight'        => 'nullable|integer|between:0,100',
            'attendance_weight'    => 'nullable|integer|between:0,100',
            'assignment_load'      => 'required|string|in:'.implode(',', array_keys(CampusOptions::LEVELS)),
            'remote_level'         => 'required|string|in:'.implode(',', array_keys(CampusOptions::LEVELS)),
            'materials'            => 'nullable|string|max:2000',
            'class_size'           => 'required|string|in:'.implode(',', array_keys(CampusOptions::CLASS_SIZES)),
            'academic_year'        => 'required|integer|between:1900,'.now()->year,
            'semester'             => 'required|string|in:'.implode(',', array_keys(CampusOptions::SEMESTERS)),
            'body'                 => 'required|string|min:20|max:500',
        ];
        foreach (CampusOptions::RATINGS as $key => $label) {
            $rules[$key] = 'required|integer|between:1,5';
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return CampusOptions::RATINGS + [
            'course'            => '授業名', 'professor' => '教授名', 'faculty' => '学部', 'department' => '学科',
            'user_type'         => 'ユーザの属性', 'has_past_exam' => '過去問あり/なし',
            'test_weight'       => 'テストの割合', 'report_weight' => 'レポートの割合', 'attendance_weight' => '出席の割合',
            'assignment_load'   => '課題の量', 'remote_level' => 'リモート割合',
            'materials'         => '教材', 'class_size' => '履修人数',
            'academic_year'     => '履修年度', 'semester' => '前期・後期', 'body' => '授業に対するコメント',
        ];
    }
}
