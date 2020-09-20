<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Application extends Model
{
    protected $collection = "applications";

    protected $fillable = [
        "bot_token",
        "guild_id",
        "name",
        "discriminator",
        "avatar",
        "owner_id",
        "prefix",
    ];

    public function owner() {
        return $this->belongsTo(User::class, "owner_id", "_id");
    }
}
