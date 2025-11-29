<?php

namespace One\Core\Services\Methods;

use One\Core\Files\Filemanager;

trait FileMethods
{
    /**
     * @var \One\Core\Files\Filemanager $filemanager
     */
    protected $filemanager = null;
    
    public function initFile()
    {
        $this->filemanager = new Filemanager();
    }
    public function getFilemanager()
    {
        return $this->filemanager;
    }
    public function setFilemanager($filemanager)
    {
        $this->filemanager = $filemanager;
    }
    
}