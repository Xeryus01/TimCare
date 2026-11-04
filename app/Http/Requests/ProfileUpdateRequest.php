<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone_number')) {
            // Terima input seperti "+62 812-3456", "0812 3456", atau "62812..."
            // lalu simpan sebagai angka saja (tanpa +, spasi, atau tanda hubung).
            $phone = preg_replace('/[^0-9]/', '', (string) $this->input('phone_number'));
            $this->merge(['phone_number' => $phone === '' ? null : $phone]);
        }
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Nomor HP hanya boleh berisi angka, contoh: 081234567890 atau 6281234567890.',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{8,20}$/'],
        ];
    }
}
