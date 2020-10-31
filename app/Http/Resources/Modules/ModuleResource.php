<?php

namespace App\Http\Resources\Modules;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public static $wrap = "module";

    private function customRules($default, $request) 
    {
        if($request->route() !== null && $request->route()->getName() !== "modules.index") {
            $default["data"] = $this->data;
        }

        return $default;
    }

    /**
     * Transform the resource into an array.
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $default = [
            "id"            => $this->id,
            "channels"      => $this->channels,
            "roles"         => $this->roles,
            "type"          => $this->type,
            "category"      => $this->category,
            "response"      => $this->response
        ];
        $default = $this->customRules($default, $request);

        return $default;
    }
}
