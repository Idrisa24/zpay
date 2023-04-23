<?php

namespace Saidtech\Zpay\Facades;

use Illuminate\Support\Facades\Facade;

class Zpay extends Facade
{
    
    protected static function getFacadeAccessor()
    {
        return 'zpay';
    }
    

}