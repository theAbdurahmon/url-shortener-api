<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLinkRequest extends FormRequest
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
            "original_url" => "required|url:http,https",
            "slug" => "regex:/[a-zA-Z]/|regex:/[0-9]/|min:6|max:12|unique:links,slug",
            "title" => "string|min:3",
            "expires_at" => Rule::date()->format("Y-m-d"),
            "password" => "string|min:6",
            "click_limit" => "integer:strict",
        ];
    }
}
