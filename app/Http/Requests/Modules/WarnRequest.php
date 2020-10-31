<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;

class WarnRequest extends FormRequest
{
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
            "app_id"        => "required|string",
            "author_id"     => "required|string",
            "user_id"       => "required|string",
            "reason"        => "required|string",
        ];
    }
    
    public function validationData()
    {
        $data = $this->all();
        $data["app_id"] = \Route::input("id");
        if(!isset($data["reason"])) {
            $data["reason"] = "No reason provided...";
        }

        return $data;
    }
}
