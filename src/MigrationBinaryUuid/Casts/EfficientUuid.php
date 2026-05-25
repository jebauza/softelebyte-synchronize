<?php


namespace Softelebyte\MigrationBinaryUuid\Casts;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EfficientUuid implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (trim($value) === '') {
            return;
        }

        return Uuid::fromBytes($value)->toString();
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (trim($value) === '') {
            return;
        }

        return [
            $key => Uuid::fromString(strtolower($value))->getBytes(),
        ];
    }
}