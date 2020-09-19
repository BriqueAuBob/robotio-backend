<?php

namespace App\Http\Resources\Tokens;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{
    public static $wrap = 'token';

    /**
     * Transform the resource into an array.
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $default = [ 
            'token' => 'Bearer '.$this->access_token,
            'redirect' => 'http://localhost:8080/panel'
        ];

        return $default;
    }
}
