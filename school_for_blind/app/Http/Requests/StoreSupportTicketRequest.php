<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketRequest extends FormRequest
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
            'message' => 'required_without:audio|prohibits:audio|nullable|string',

            'audio' => 'required_without:message|prohibits:message|nullable|file|mimes:mp3,wav,m4a,mp4,aac,mpga|max:10240',

            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];

    }

    public function messages(): array
    {
        return [
            'message.required_without' => 'لا يمكنك إرسال صورة فقط، يرجى كتابة تفاصيل المشكلة أو إرفاق تسجيل صوتي.',
            'audio.required_without' => 'لا يمكنك إرسال صورة فقط، يرجى إرفاق تسجيل صوتي للمشكلة أو كتابة نص.',
            'message.required_without_all' => 'يرجى إرسال تفاصيل المشكلة (نص، أو تسجيل صوتي، أو إرفاق صورة).',
            'audio.required_without_all' => 'يرجى إرسال تفاصيل المشكلة (نص، أو تسجيل صوتي، أو إرفاق صورة).',
            'image.required_without_all' => 'يرجى إرسال تفاصيل المشكلة (نص، أو تسجيل صوتي، أو إرفاق صورة).',
            'message.prohibits' => 'لا يمكنك إرسال نص وتسجيل صوتي معاً في نفس التذكرة.',
            'audio.prohibits' => 'لا يمكنك إرسال تسجيل صوتي ونص معاً في نفس التذكرة.',
            'message.string' => 'يجب أن يكون محتوى المشكلة من نوع نصي (String).',
            'audio.file' => 'يجب أن يكون المرفق الصوتي ملفاً صالحاً.',
            'audio.mimes' => 'صيغة الملف الصوتي غير مدعومة. الصيغ المقبولة هي: mp3, wav, ogg, m4a.',
            'audio.max' => 'حجم الملف الصوتي يجب أن لا يتجاوز 10 ميغابايت.',
            'image.image' => 'يجب أن يكون المرفق صورة صحيحة.',
            'image.mimes' => 'صيغة الصورة غير مدعومة. الصيغ المقبولة هي: jpeg, png, jpg.',
            'image.max' => 'حجم الصورة يجب أن لا يتجاوز 5 ميغابايت.',

        ];
    }
}
