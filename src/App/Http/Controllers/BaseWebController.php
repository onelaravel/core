<?php

namespace One\App\Http\Controllers;

use One\App\Http\Controllers\Controller;
use One\App\Http\Controllers\Support\WebResponse;
use One\Core\Support\Methods\CacheMethods;
use One\Core\Support\Methods\ResponseMethods;
use One\Core\Support\Methods\ViewMethods;

abstract class BaseWebController extends Controller
{
    use ViewMethods, ResponseMethods, WebResponse, CacheMethods;

    public function moduleKey($action = null){
        return $this->context . '.' . $this->module . ($action ? '.' . $action : '');
    }
}