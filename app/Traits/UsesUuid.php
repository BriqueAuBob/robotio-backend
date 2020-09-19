<?php

namespace App\Traits;

use Str;

trait UsesUuid
{
    protected static function bootUsesUuid(): void
    {
        static::creating(static function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = str_replace("-", "", (string) Str::uuid());
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
