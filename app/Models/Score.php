<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Score extends Model
{
    protected $collection = "guilds_members_scores";

    protected $fillable = [
        "user_id",
        "app_id",
        "amount",
    ];
}