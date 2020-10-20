<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class LogFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    * @var array
    */
    public $relations = [];

    protected $filterables = ["content", "module"];

    public function content(string $content): LogFilter
    {
        return $this->where("content", 'LIKE', "%{$content}%");
    }

    public function module(string $module): LogFilter
    {
        return $this->where("module", $module);
    }

    public function date(string $date): LogFilter
    {
        $unjson = json_decode($date);
        $first = \Carbon\Carbon::parse($unjson->first);
        $second = \Carbon\Carbon::parse($unjson->second);
        return $this->whereBetween("created_at", [$first, $second]);
    }
}
