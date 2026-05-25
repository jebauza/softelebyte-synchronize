<?php


namespace Softelebyte\Synchronize\Base\Transformer;


use ReflectionClass;
use ReflectionProperty;

Trait ToArrayAllProperties
{
    public function toArray()
    {
        $me = [];
        $reflect = new ReflectionClass(self::class);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        foreach ($props as $prop) {
            $key =$prop->getName();
            if(isset($this->$key)) {
                $me[$key] = $this->$key;
            }
        }

        return $me;
    }
}
