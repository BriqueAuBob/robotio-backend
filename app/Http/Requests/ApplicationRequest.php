<?php

namespace App\Http\Requests;

use \Auth;

use Illuminate\Foundation\Http\FormRequest;

/*
 * TODO: Writing field rules that can be updated by user
 */
class ApplicationRequest extends FormRequest
{
    public function messages()
    {
        return [
            "prefix.min" => "Votre préfixe est trop court!",
            "prefix.max" => "Votre préfixe est trop long!",
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
        ];
    }
}
