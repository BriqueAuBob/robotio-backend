<?php

namespace App\Http\Requests;

use \Auth;

use Illuminate\Foundation\Http\FormRequest;

/*
 * TODO: Writing field rules that can be updated by user
 */
class ModuleRequest extends FormRequest
{
    public function messages()
    {
        return [
            "channel.string" => "Le channel est invalide.",
            "roles.array" => "Le champs 'rôles' dois être un tableau.",
            "permissions.array" => "Le champs 'permissions' dois être un tableau.",
            "response.string" => "La response est invalide.",
            "type.string" => "Le type est invalide.",
            "category.string" => "La category est invalide.",
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
            "channel" => "string",
            "roles" => "array",
            "permissions" => "array",
            "data" => "",
            "embed" => "",
            "response" => "string",
            "type" => "string",
            "category" => "string",
        ];
    }
}
