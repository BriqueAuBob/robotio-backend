<?php

namespace App\Http\Resources\Applications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApplicationCollection extends ResourceCollection
{

    public $collects = ApplicationResource::class;
    public static $wrap = 'applications';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'applications' => $this->collection,
            'count' => $this->collection->count()
        ];
    }
}
