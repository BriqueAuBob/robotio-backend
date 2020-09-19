<?php

namespace App\Http\Requests\Users;

use \Auth;

use Illuminate\Foundation\Http\FormRequest;

/*
 * TODO: Writing field rules that can be updated by user
 */
class UpdateRequest extends FormRequest
{
    public function messages()
    {
        return [
            'slug.unique' => 'Ce lien personnalisé est déjà utilisé!',
            'slug.max' => 'Ce lien personnalisé est trop long!',
            'headline.max' => 'Cette description est trop longue!',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        if(($this->slug or $this->headline) && !Auth::user()->hasRole('michel') && Auth::user()["discord_id"] !== 307531336388968458) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        return [
            'slug' => 'nullable|unique:users,slug|max:30',
            'headline' => 'nullable|max:60',
            'birthday' => 'nullable|date_format:Y-m-d'
        ];
    }
}
