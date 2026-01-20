<?php

namespace One\App\Http\Controllers;

use One\Core\Events\EventMethods;
use One\Core\Support\Methods\SmartInit;
abstract class Controller
{
    use EventMethods, SmartInit;
    protected $service = null;
    public function __construct()
    {
        $this->init();
    }


    
}