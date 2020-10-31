<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Module extends Model
{
    protected $collection = "modules";

    protected $fillable = [
        "channels",
        "roles",
        "data",
        "embed",
        "type",
        "category",
        "response"
    ];

    public function owner() {
        return $this->belongsTo(User::class, "owner_id", "_id");
    }
}
