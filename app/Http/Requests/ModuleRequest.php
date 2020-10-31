<?php

namespace App\Http\Requests;

use \Auth;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    public function messages()
    {
        return [
            "channels.string" => "Le channel est invalide.",
            "roles.array" => "Le champs 'rôles' dois être un tableau.",
            "permissions.array" => "Le champs 'permissions' dois être un tableau.",
            "response.string" => "La response est invalide.",
            "type.required" => "Le type est requis.",
            "type.string" => "Le type est invalide.",
            "category.required" => "Le type est pas présent dans notre liste de modules.",
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
            "channels" => "nullable|array",
            "roles" => "nullable|array",
            "data" => "nullable",
            "type" => "required|string",
            "category" => "required|string",
            "response" => "nullable",
        ];
    }

    public function validationData()
    {
        $modules = config("ro-bot.modules");
        $data = $this->all();

        if(!isset($data["type"]) || !isset($modules[$data["type"]])) return $data;

        $data["category"] = $modules[$data["type"]];
        return $data;
    }
}
