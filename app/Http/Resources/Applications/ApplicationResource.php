<?php

namespace App\Http\Resources\Applications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Auth;

class ApplicationResource extends JsonResource
{
    public static $wrap = "application";

    /**
     * Transform the resource into an array.
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $user = Auth::user();
        $guild = collect($user->guilds)->where("id", $this->guild_id)->first();

        return [
            "guild_id"          => $this->guild_id,
            "guild"          => $guild,
            "owner"             => $this->owner,
            "id"                => $this->_id,
            "name"              => $this->name,
            "discriminator"     => $this->discriminator,
            "avatar"            => $this->avatar,
            "channels"          => $this->channels,
            "roles"             => $this->roles,
            "emojis"            => $this->emojis,
            "created_at"        => $this->created_at
        ];
    }
}
