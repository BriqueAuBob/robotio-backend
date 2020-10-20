<?php

namespace App\Http\Requests;

use \Auth;

use Illuminate\Foundation\Http\FormRequest;

class LogRequest extends FormRequest
{
    public function messages()
    {
        return [
            "channel.string" => "Le channel est invalide.",
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
            "app_id"    => "required|string",
            "module"    => "required|string",
            "type"      => "required|string",
            "content"   => "required|string",
        ];
    }
    
    public function validationData()
    {
        return array_merge(parent::all(), [
            "app_id" => \Route::input("id")
        ]);
    }
}
