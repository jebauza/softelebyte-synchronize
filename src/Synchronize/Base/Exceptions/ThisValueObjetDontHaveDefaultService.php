<?php


namespace Softelebyte\Synchronize\Base\Exceptions;


use Exception;
use Throwable;

class ThisValueObjetDontHaveDefaultService extends Exception
{
    protected string $class;

    public function __construct(string $class, $code = 0, Throwable $previous = null)
    {
        $this->class = $class;
        $message = 'La clase de sincronización: ' . $class . ' no tiene un servicio asociado.';
        parent::__construct($message, $code, $previous);
    }

    public function getSynchroClass(): string
    {
        return $this->class;
    }
}
