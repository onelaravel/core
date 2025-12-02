<?php

namespace One\Core\Support\Methods;

use One\Core\Engines\ViewContextManager;
use Illuminate\Support\Facades\App;

/**
 * ViewMethods - Trait quản lý view với ViewContextManager
 * 
 * Service sử dụng trait này cần có:
 * - $context: Tên context (admin, web, ...)
 * - $module: Tên module
 * - $moduleName: Tên hiển thị của module (optional)
 * 
 * ViewContextManager được lấy từ service container (singleton)
 */
trait ViewMethods
{
    /**
     * @var string $context Context của service
     */
    protected $context = '';

    /**
     * @var string $module Tên module (cũng là tên thư mục view)
     */
    protected $module = 'test';

    /**
     * @var string $moduleName Tên hiển thị của module
     */
    protected $moduleName = '';
    
    /**
     * Lấy ViewContextManager từ container
     * 
     * @return ViewContextManager
     */
    protected function getViewContextManager(): ViewContextManager
    {
        return App::make(ViewContextManager::class);
    }

    /**
     * Render view
     * 
     * Nếu có module: render module view (context.modules.module.blade)
     * Nếu không có module: render từ base (context.blade)
     * 
     * @param string $blade Tên blade
     * @param array $data Dữ liệu
     * @return \Illuminate\Contracts\View\View
     */
    public function render(string $blade, array $data = [])
    {
        $context = $this->context ?: 'web';
        $contextManager = $this->getViewContextManager();

        // Merge với module info
        $mergedData = array_merge([
            'module_slug' => $this->module,
            'module_name' => $this->moduleName,
            'route_name_prefix' => $this->routeNamePrefix ?? '',
            'package' => $this->package ?? null,
        ], $data);

        // Nếu có module và module không rỗng, render module view
        // Module mặc định 'test' được coi là không có module
        if (!empty($this->module) && $this->module !== 'test') {
            return $contextManager->renderModule($context, $this->module, $blade, $mergedData);
        }

        // Nếu không có module, render từ base: context.blade
        // Ví dụ: context='web', blade='abc' => 'web.abc'
        return $contextManager->render($context, '', $blade, $mergedData, '');
    }

    /**
     * Render module view
     * 
     * @param string $blade Tên blade
     * @param array $data Dữ liệu
     * @return \Illuminate\Contracts\View\View
     */
    public function renderModule(string $blade, array $data = [])
    {
        $context = $this->context ?: 'web';
        $contextManager = $this->getViewContextManager();

        $mergedData = array_merge([
            'module_slug' => $this->module,
            'module_name' => $this->moduleName,
        ], $data);

        return $contextManager->renderModule($context, $this->module, $blade, $mergedData);
    }

    /**
     * Render page view
     * 
     * @param string $page Tên page
     * @param array $data Dữ liệu
     * @return \Illuminate\Contracts\View\View
     */
    public function renderPage(string $page, array $data = [])
    {
        $context = $this->context ?: 'web';
        $contextManager = $this->getViewContextManager();

        $mergedData = array_merge([
            'module_slug' => $this->module,
            'module_name' => $this->moduleName,
        ], $data);

        return $contextManager->renderPage($context, $this->module, $page, $mergedData);
    }

    /**
     * Render component view
     * 
     * @param string $component Tên component
     * @param array $data Dữ liệu
     * @return \Illuminate\Contracts\View\View
     */
    public function renderComponent(string $component, array $data = [])
    {
        $context = $this->context ?: 'web';
        $contextManager = $this->getViewContextManager();

        return $contextManager->renderComponent($context, $component, $data);
    }

    /**
     * Render layout view
     * 
     * @param string $layout Tên layout
     * @param array $data Dữ liệu
     * @return \Illuminate\Contracts\View\View
     */
    public function renderLayout(string $layout, array $data = [])
    {
        $context = $this->context ?: 'web';
        $contextManager = $this->getViewContextManager();

        return $contextManager->renderLayout($context, $layout, $data);
    }

    /**
     * Render template view
     * 
     * @param string $template Tên template
     * @param array $data Dữ liệu
     * @return \Illuminate\Contracts\View\View
     */
    public function renderTemplate(string $template, array $data = [])
    {
        $context = $this->context ?: 'web';
        $contextManager = $this->getViewContextManager();

        return $contextManager->renderTemplate($context, $template, $data);
    }
}
