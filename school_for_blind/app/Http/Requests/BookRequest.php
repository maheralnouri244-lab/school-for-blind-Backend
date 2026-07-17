<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
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
             'recording_id' => 'required|exists:records,id',
        'name' => 'nullable|string|max:255',
        'lesson_id' => 'required|exists:lessons,id',
        'timestamp_in_seconds' => [
            'required',
            'integer',
            'min:0', 
            Rule::unique('bookmarks')->where(function ($query) {
                return $query->where('student_id', Auth::id()) 
                             ->where('record_id', $this->recording_id);
            })
        ],
        ];
    }

public function messages(): array
    {
        return [
            'recording_id.required' => 'حقل معرف التسجيل مطلوب.',
            'recording_id.exists' => 'التسجيل المحدد غير موجود.',
            'name.required' => 'حقل الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصًا.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
            'timestamp_in_seconds.required' => 'حقل الطابع الزمني مطلوب.',
            'timestamp_in_seconds.integer' => 'يجب أن يكون الطابع الزمني عددًا صحيحًا.',
            'timestamp_in_seconds.min' => 'يجب أن يكون الطابع الزمني صفرًا أو أكبر.',
            'timestamp_in_seconds.unique' => 'لقد قمت بإضافة علامة مرجعية عند هذا الوقت مسبقاً لهذا الدرس.',
        ];
    }
}
