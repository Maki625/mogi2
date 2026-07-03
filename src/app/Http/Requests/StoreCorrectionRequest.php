<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreCorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'clock_in' => ['required'],
            'clock_out' => ['required', 'after:clock_in'],

            'break1_start' => ['nullable'],
            'break1_end' => ['nullable'],

            'break2_start' => ['nullable'],
            'break2_end' => ['nullable'],

            'reason' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_out.required' => '退勤時間を入力してください',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break1_end.after' => '休憩時間が不適切な値です',
            'break2_end.after' => '休憩時間が不適切な値です',
            'reason.required' => '備考を記入してください',
        ];
    }

public function withValidator($validator)
{
    $validator->after(function ($validator) {

        $in = \Carbon\Carbon::parse(request('clock_in'));
        $out = \Carbon\Carbon::parse(request('clock_out'));

        // ② 休憩1
        $b1s = request('break1_start') ? Carbon::parse(request('break1_start')) : null;
        $b1e = request('break1_end') ? Carbon::parse(request('break1_end')) : null;

        if ($b1s && ($b1s < $in || $b1s > $out)) {
    $validator->errors()->add(
                'break1_start',
                '休憩時間が不適切な値です'
            );
        }

        if ($b1e && ($b1e > $out)) {
            $validator->errors()->add(
                'break1_end',
                '休憩時間もしくは退勤時間が不適切な値です'
            );
        }

        // ③ 休憩2
        $b2s = request('break2_start') ? Carbon::parse(request('break2_start')) : null;
        $b2e = request('break2_end') ? Carbon::parse(request('break2_end')) : null;

        if ($b2s && ($b2s < $in || $b2s > $out)) {
            $validator->errors()->add(
                'break2_start',
                '休憩時間が不適切な値です'
            );
        }

        if ($b2e && ($b2e > $out)) {
            $validator->errors()->add(
                'break2_end',
                '休憩時間もしくは退勤時間が不適切な値です'
            );
        }
    });
}

}
