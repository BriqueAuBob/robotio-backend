<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Module extends Model
{
    protected $collection = "modules";

    protected $fillable = [
        "channel",
        "roles",
        "permissions",
        "data",
        "embed",
        "response",
        "type",
        "category",
    ];

    public function owner() {
        return $this->belongsTo(User::class, "owner_id", "_id");
    }
}
