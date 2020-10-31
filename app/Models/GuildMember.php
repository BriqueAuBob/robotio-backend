<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class GuildMember extends Model
{
    protected $collection = "guilds_members";

    protected $fillable = [
        "discord_id",
        "username",
        "tag",
        "avatar",
    ];

    public function getRouteKeyName()
    {
        return "discord_id";
    }
}