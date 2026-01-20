<?php

namespace One\App\Http\Controllers;

use One\App\Http\Controllers\Controller;
use One\App\Http\Controllers\Support\ApiResponse;
use One\Core\Support\Methods\CacheMethods;

abstract class BaseApiController extends Controller
{
    use ApiResponse, CacheMethods;

}