<?php

namespace Taitech\Azampay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed checkout(array $payload)
 * @method static string generateToken()
 * @method static mixed __call(string $method, array $arguments)
 * 
 * @see \Taitech\Azampay\AzampayService
 */
class AzampayFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'azampay';
    }
}
