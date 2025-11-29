<?php

namespace One\Core\Services;

use One\Core\Services\Methods\ModuleMethods;
use One\Core\Services\Methods\CRUDMethods;
use One\Core\Services\Methods\CacheMethods;
use One\Core\Services\Methods\ResponseMethods;

class ModuleService extends Service
{
    use ModuleMethods, CRUDMethods, CacheMethods;

    

}