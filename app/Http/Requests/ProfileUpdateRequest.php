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
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', Rule::in(['หัวหน้า', 'พนักงานปกติ'])],
            'department' => ['nullable', Rule::in(['programer', 'product', 'marketing', 'admin', 'hr', 'manager', 'editor', 'finance'])],
        ];
        // เฉพาะ head เท่านั้นที่สามารถแก้ไข role ได้
        if (auth()->user() && auth()->user()->role === 'head') {
            $rules['role'] = ['required', Rule::in(['owner', 'head', 'agent', 'user'])];
        }
        return $rules;
    }
}
