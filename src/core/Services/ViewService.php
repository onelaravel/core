<?php

namespace One\Core\Services;

use One\Core\Engines\CacheEngine;


class ViewService extends Service
{
    use Methods\ViewMethods;
    protected $cacheTime = 0;
    public function __construct()
    {
        parent::__construct();
        $this->viewInit();
        $this->cacheKey = md5(static::class);
        
    }



    public function __call($method, $params)
    {
        $name = preg_replace('/Cache$/i', '', $method);
        if($name != $method && method_exists($this, $name)){
            return CacheEngine::cache($this->cacheKey . '-' . $name, $params, function() use($params, $name){
                return $this->{$name}(...$params);
            }, $this->cacheTime);
        }
        return parent::__call($method, $params);
    }
}
