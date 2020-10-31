<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function messages()
    {
        return [
            "prefix.min" => "Votre préfixe est trop court!",
            "prefix.max" => "Votre préfixe est trop long!",
            "language.min" => "Votre language est trop court!",
            "language.max" => "Votre language est trop long!",
            "collaborators.array" => "Le champs collaborators doit être un tableau!",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        return [
            "prefix" => "min:1|max:4",
            "language" => "min:1|max:3",
            "collaborators" => "nullable|array",
        ];
    }
}
