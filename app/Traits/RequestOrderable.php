<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait RequestOrderable
{
    /**
     * Ability to handle ordering and sorting directly from a Request object.
     * @param Builder $query
     * @param Request $request
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeOrderable(Builder $query, Request $request)
    {
        return $query->orderBy($request->get('filter') ?? $this->getKeyName(), $request->get('direction') ?? 'desc');
    }
}
