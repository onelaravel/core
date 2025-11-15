<?php
namespace One\Core\Engines;

use One\Core\Repositories\Html\AreaRepository;
use One\Core\Repositories\Html\HtmlAreaList;
use One\Core\Repositories\Html\Options;
use One\Core\Files\Filemanager;
use One\Core\Helpers\Arr;

class ViewDataEngine
{
    static $shared = false;

    
    public static function share($name = null, $value=null)
    {
        if(static::$shared) return true;;
        $a = $name?(is_array($name)?$name:(is_string($name)?[$name=>$value]: [])):[];
        view()->share($a);

        static::$shared = true;

        return true;
    }
}
