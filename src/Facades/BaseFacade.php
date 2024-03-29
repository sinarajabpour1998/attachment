<?php

namespace Sinarajabpour1998\Attachment\Facades;

use Illuminate\Support\Facades\Facade;

class BaseFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return static::class;
    }

    public static function shouldProxyTo($class)
    {
        app()->bind(self::getFacadeAccessor(), $class);
    }
}
