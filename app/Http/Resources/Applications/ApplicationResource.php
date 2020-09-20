<?php

namespace App\Http\Resources\Applications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\User;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class ApplicationResource extends JsonResource
{
    public static $wrap = "application";

    private function customRules($default, $request) 
    {
        if ($request->user("api")) {
            $user = $request->user("api");
            $guild = collect($user->guilds)->where("id", $this->guild_id)->first();

            $default["guild"] = $guild;
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
            "owner"             => $this->owner,
            "id"                => $this->_id,
            "name"              => $this->name,
            "discriminator"     => $this->discriminator,
            "avatar"            => $this->avatar,
            "channels"          => $this->channels,
            "roles"             => $this->roles,
            "emojis"            => $this->emojis,
            "bot_token"         => $this->bot_token,
            "modules"            => isset($this->modules) ? $this->modules : null,
            "created_at"        => $this->created_at
        ];
        $default = $this->customRules($default, $request);

        return $default;
    }
}
