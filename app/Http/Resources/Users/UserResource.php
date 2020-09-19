<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Tokens\TokenResource;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Warn;
use App\Http\Requests\WarnRequest;
use App\Http\Resources\WarnCollection;

use App\Http\Resources\ExperienceCollection;
use App\Http\Requests\ExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;

use App\Http\Resources\Tutorials\TutorialCollection;

class UserResource extends JsonResource
{
    public static $wrap = "";

    /**
     * Transform the resource into an array.
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $key = ( $request->route() !== null && $request->route()->getName() === "me.get" ) ? "me" : "user";
        $default = [
            $key => [
                "discord_id"            => strval($this->discord_id),
                "username"              => $this->username,
                "tag"                   => $this->tag,
                "avatar"                => url($this->avatar === "gif" ? "/avatars/{$this->discord_id}.gif" : "/avatars/{$this->discord_id}.png"),
                "last_activity"         => $this->last_activity ?? false,
                "guilds"                => $this->guilds,
                "tokens"                => isset($this->money) ? $this->money : 0,
                "created_at"            => $this->created_at,
            ]
        ];

        return $default;
    }
}
