<?php

namespace App\Http\Resources\Applications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Module;

class ApplicationResource extends JsonResource
{
    public static $wrap = "application";

    private function customRules($default, $request) 
    {
        if ($request->user("api")) {
            $user = $request->user("api");
            $guild = collect($user->guilds)->where("id", $this->guild_id)->first();

            $default["guild"] = $guild;
            $default["modules"] = isset($this->modules) ? collect($this->modules)->map(static function($item, $key) {
                return $item["id"];
            }) : null;
        } else {
            $default["modules"] = collect($this->modules)->map(static function($item, $key) {
                $data = Module::where("_id", $item["id"])->first();
                return [
                    "id"            => $item["id"],
                    "type"          => $item["type"],
                    "category"      => $data->category,
                    "data"          => $data->data,
                    "roles"         => $data->roles,
                    "channels"      => $data->channels,
                    "response"      => $data->response
                ];
            });
        }

        if($request->collaborator && $request->collaborator === "admin") {
            $default["role"] = $request->collaborator;
        } elseif ($request->collaborator && $request->collaborator === "modo") {
            $default["role"] = $request->collaborator;
            $default["modules"] = null;

            $default["channels"] = null;
            $default["roles"] = null;
            $default["emojis"] = null;

            $default["errors"] = null;
        } else {
            $default["role"] = "owner";
            $default["bot_token"] = $this->bot_token;
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
            "guild_id"          => $this->guild_id,
            "prefix"            => isset($this->prefix) ? $this->prefix : "/",
            "id"                => $this->_id,
            "name"              => $this->name,
            "discriminator"     => $this->discriminator,
            "avatar"            => $this->avatar,
            "channels"          => $this->channels,
            "roles"             => $this->roles,
            "emojis"            => $this->emojis,
            "language"          => $this->language,
            "errors"            => $this->errors,
            "collaborators"     => $this->collaborators,
            "created_at"        => $this->created_at
        ];
        $default = $this->customRules($default, $request);

        return $default;
    }
}
