<?php

namespace App\Models\Modules;

use Jenssegers\Mongodb\Eloquent\Model;
use App\Models\GuildMember;

class Warn extends Model
{
    protected $collection = "warns";

    protected $fillable = [
        "app_id",
        "author_id",
        "user_id",
        "reason",
    ];

    public function author() {
        return $this->belongsTo(GuildMember::class, "author_id", "discord_id");
    }

    public function user() {
        return $this->belongsTo(GuildMember::class, "user_id", "discord_id");
    }
}
