<?php

namespace Mydnic\KanpenFilamentPlugin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mydnic\KanpenFilamentPlugin\KanpenFilamentPlugin
 */
class KanpenFilamentPlugin extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mydnic\KanpenFilamentPlugin\KanpenFilamentPlugin::class;
    }
}
