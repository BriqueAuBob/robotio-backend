<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use EloquentFilter\Filterable;

class Log extends Model
{
    use Filterable;
    
    protected $collection = "logs";

    protected $fillable = [
        "app_id",
        "module",
        "type",
        "content",
    ];

    protected $dates = ['created_at','updated_at'];
}
