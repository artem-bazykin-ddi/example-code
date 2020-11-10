<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class RouterPathFacade
 * @package App\Facades
 *
 *
 * @method static void includeRoutes(string $folder)
 * @see \App\Tools\RouterPath
 */

class RouterPathFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'routerpath';
    }

}