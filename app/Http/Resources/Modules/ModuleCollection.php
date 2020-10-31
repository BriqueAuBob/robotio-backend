<?php

namespace App\Http\Resources\Modules;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ModuleCollection extends ResourceCollection
{

    public $collects = ModuleResource::class;
    public static $wrap = 'modules';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'modules' => $this->collection,
            'count' => $this->collection->count()
        ];
    }
}
