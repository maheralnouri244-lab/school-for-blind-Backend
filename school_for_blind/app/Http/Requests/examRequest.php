<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class examRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'exam_id' => 'required|integer|exists:exams,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.choice_id' => 'nullable|integer|exists:choices,id',
            'answers.*.text_answer' => 'nullable|string|prohibits:answers.*.audio_answer',
            'answers.*.audio_answer' => 'nullable|file|mimes:mp3,wav,m4a,mp4,aac,mpga|max:10240|prohibits:answers.*.text_answer',        ];

    }

    public function messages(): array
    {
        return [
            'answers.*.text_answer.prohibits' => 'لا يمكن تقديم إجابة نصية وإجابة صوتية لنفس السؤال.',
            'answers.*.audio_answer.prohibits' => 'لا يمكن تقديم إجابة صوتية وإجابة نصية لنفس السؤال.',
            'answers.*.audio_answer.mimes' => 'يجب أن يكون الملف الصوتي من نوع mp3 أو wav أو m4a.',
            'answers.*.audio_answer.max' => 'يجب ألا يتجاوز حجم الملف الصوتي 10 ميغابايت.',
            'exam_id.required' => 'رقم الاختبار مطلوب.',
            'exam_id.exists' => 'الاختبار المحدد غير موجود لدينا.',
            'answers.*.question_id.required' => 'رقم السؤال مطلوب لكل إجابة.',
        ];
    }
}
