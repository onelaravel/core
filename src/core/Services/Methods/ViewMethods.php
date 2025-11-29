<?php

namespace One\Core\Services\Methods;

use Illuminate\Http\Request;
use One\Core\Magic\Arr;

trait ViewMethods
{

    /**
     * @var string $context context cua service
     */
    protected $context = '';
    /**
     * @var string $module day là tên module cung la ten thu muc view va ten so it cua bang, thu muc trong asset
     * override del chinh sua
     */
    protected $module = 'test';


    /**
     * @var string $moduleName tên của module và cũng là tiêu đề trong form
     */
    protected $moduleName = '';
    
    /**
     * @var string $viewFolder thu muc chua view
     * khong nen thay doi lam gi
     */
    protected $viewFolder = null;

    /**
     * @var string $index file do data cua ham index
     */
    protected $index = 'index';

    /**
     * @var string $list file do data cua ham list
     */
    protected $list = 'list';

    /**
     * @var string $trash file do data cua ham trash
     */
    protected $trash = 'trash';

    /**
     * @var string $detail ten file blade cua detail
     */
    protected $detail = 'detail';

    /**
     * @var string $form blade name
     */
    protected $form = 'form';

    /**
     * @var string $alert blade name
     */
    protected $alert = 'alert';

    /**
     * @var string $error blade name
     */
    protected $error = 'errors';

    protected $moduleBlade = null;

    protected $pageViewBlade = null;

    protected $viewMode = 'direct'; // direct, package, module, theme

    protected $mode = 'system';

    protected $viewBasePath = null;

    protected $defaultViewData = [
        '__system__' => '_system.',
        '__base__' => 'web.',
        'module_slug' => 'web',
        'module_name' => 'Web',
        '__route_prefix__' => 'web.',
        '__component__' => 'web.components.',
        '__template__' => 'web.templates.',
        '__pagination__' => 'web.pagination.',
        '__layout__' => 'web.layouts.',
        '__module__' => 'web.modules.',
        '__page__' => 'web.pages.',
    ];

    public function initView()
    {
        if (!$this->moduleBlade) $this->moduleBlade = $this->module;
        $this->viewBasePath = ($this->context ? '.' . $this->context : '') . ($this->viewFolder ? '.' . $this->viewFolder : '');
        $this->moduleBlade = ($this->viewBasePath ? '.' . $this->viewBasePath : '') . '.modules';
        $d = $this->viewBasePath ? $this->viewBasePath . '.' : '';
        $this->pageViewBlade = $d . 'pages.';
        $this->defaultViewData = [
            '__system__' => '_system.',
            '__base__' => $d,
            'module_slug' => $this->module,
            'module_name' => $this->moduleName,
            '__route_prefix__' => $this->routeNamePrefix,
            '__component__' => $d . 'components.',
            '__template__' => $d . 'templates.',
            '__pagination__' => $d . 'pagination.',
            '__layout__' => $d . 'layouts.',
            '__module__' => $d . 'modules.',
            '__page__' => $d . 'pages.',
        ];
        return $this;
    }

    public function setViewConfig($config = [])
    {
        if (is_array($config) && count($config)) {
            $context = $config['context'] ?? $this->context;
            $viewFolder = $config['viewFolder'] ?? $this->viewFolder;
            $this->context = $context;
            $this->viewFolder = $viewFolder;
            $this->viewBasePath = $this->context . '.';
            $this->moduleBlade = ($this->viewBasePath ? '.' . $this->viewBasePath : '') . '.modules';

            $d = $this->viewBasePath ? $this->viewBasePath . '.' : '';
            $this->pageViewBlade = $d . 'pages.';

            $this->defaultViewData = [
                '__system__' => '_system.',
                '__base__' => $d,
                'module_slug' => $this->module,
                'module_name' => $this->moduleName,
                '__route_prefix__' => $this->routeNamePrefix,
                '__component__' => $d . 'components.',
                '_template' => $d . 'templates.',
                '__pagination__' => $d . 'pagination.',
                '__layout__' => $d . 'layouts.',
                '__module__' => $d . 'modules.',
                '__page__' => $d . 'pages.',
            ];
        }
        return $this;
    }



    /**
     * bắt sự kiện
     * @param string $event
     * @param array ...$params
     * @return mixed
     */
    public function callViewEvent(string $event, ...$params)
    {
        if (method_exists($this, $event)) {
            return call_user_func_array([$this, $event], $params);
        }
        $a = $this->fire($event, ...$params);

        return null;
    }

    public function getViewPath(string $bladePath)
    {
        return $this->viewBasePath . $bladePath;
    }

    public function getViewModulePath(string $bladePath)
    {
        return $this->moduleBlade . $bladePath;
    }

    public function getViewPagePath(string $bladePath)
    {
        return $this->pageViewBlade . $bladePath;
    }

    /**
     * Parse blade path với các alias: @module, @page, @base
     * 
     * Hỗ trợ các format:
     * - @module.index => {moduleBlade}index
     * - @module:list => {moduleBlade}list
     * - @page.about => {pageViewBlade}about
     * - @base.home => {viewBasePath}home
     * 
     * @param string $bladePath Đường dẫn blade có thể chứa alias
     * @return string Đường dẫn blade đã được parse
     */
    public function parseBladePath(string $bladePath)
    {
        // Kiểm tra @module alias (case-insensitive)
        if (preg_match('/^@module([\.\:])/i', $bladePath, $matches)) {
            $separator = $matches[1]; // Lấy dấu . hoặc :
            $remaining = substr($bladePath, strlen('@module' . $separator));

            // Kiểm tra moduleBlade có giá trị không
            if ($this->moduleBlade !== null && $this->moduleBlade !== '') {
                // Đảm bảo có trailing dot nếu cần
                $moduleBlade = rtrim($this->moduleBlade, '.');
                return $moduleBlade . '.' . $remaining;
            }
            // Nếu moduleBlade null/empty, trả về path gốc (có thể gây lỗi nhưng giữ nguyên behavior)
            return $bladePath;
        }

        // Kiểm tra @page alias (case-insensitive)
        if (preg_match('/^@page([\.\:])/i', $bladePath, $matches)) {
            $separator = $matches[1];
            $remaining = substr($bladePath, strlen('@page' . $separator));

            if ($this->pageViewBlade !== null && $this->pageViewBlade !== '') {
                $pageViewBlade = rtrim($this->pageViewBlade, '.');
                return $pageViewBlade . '.' . $remaining;
            }
            return $bladePath;
        }

        // Kiểm tra @base alias (case-insensitive)
        if (preg_match('/^@base([\.\:])/i', $bladePath, $matches)) {
            $separator = $matches[1];
            $remaining = substr($bladePath, strlen('@base' . $separator));

            if ($this->viewBasePath !== null && $this->viewBasePath !== '') {
                $viewBasePath = rtrim($this->viewBasePath, '.');
                return $viewBasePath . '.' . $remaining;
            }
            return $bladePath;
        }
        return ($this->viewBasePath ? $this->viewBasePath . '.' : '') . $bladePath;
    }


    /**
     * Parse blade path với các alias: @module, @page, @base
     * 
     * Hỗ trợ các format:
     * - @module.index => {moduleBlade}index
     * - @module:list => {moduleBlade}list
     * - @page.about => {pageViewBlade}about
     * - @base.home => {viewBasePath}home
     * 
     * @param string $bladePath Đường dẫn blade có thể chứa alias
     * @return string Đường dẫn blade đã được parse
     */
    public function getBladeViewRenderConfig(string $bladePath)
    {
        $config = [
            'view' => $bladePath,
            'method' => 'render',
        ];
        // Kiểm tra @module alias (case-insensitive)
        if (preg_match('/^@module([\.\:])/i', $bladePath, $matches)) {
            $separator = $matches[1]; // Lấy dấu . hoặc :
            $remaining = substr($bladePath, strlen('@module' . $separator));
            $config['view'] = $remaining;
            $config['method'] = 'renderModule';
        }

        // Kiểm tra @page alias (case-insensitive)
        elseif (preg_match('/^@page([\.\:])/i', $bladePath, $matches)) {
            $separator = $matches[1];
            $remaining = substr($bladePath, strlen('@page' . $separator));
            $config['view'] = $remaining;
            $config['method'] = 'renderPage';
        }

        // Kiểm tra @base alias (case-insensitive)
        elseif (preg_match('/^@base([\.\:])/i', $bladePath, $matches)) {
            $separator = $matches[1];
            $remaining = substr($bladePath, strlen('@base' . $separator));
            $config['view'] = $remaining;
            // $config['method'] = 'render';
        }
        return $config;
    }

    /**
     * view
     * @param string $bladePath
     * @param array $data
     * @return ViewEngine
     */
    public function render(string $bladePath, array $data = [])
    {
        $d = $this->defaultViewData['__base__'];

        $bp = $d . $bladePath;

        if ($this->isViewForm) {
            if (!view()->exists($bp) && $this->mode == 'package') {
                $d = $this->package . ':' . $d;
                $bp = $this->package . ':' . $bp;
            }
        } elseif ($this->mode == 'package' && view()->exists($this->package . ':' . $bp)) {
            $d = $this->package . ':' . $d;
            $bp = $this->package . ':' . $bp;
        }


        $a = explode('.', $bp);
        $b = array_pop($a);
        $current = implode('.', $a) . '.';
        $mdd = [
            '_current' => $current,
            'module_slug' => $this->module,
            'module_name' => $this->moduleName,
            'route_name_prefix' => $this->routeNamePrefix,
            'package' => $this->package
        ];
        if ($this->mode != 'package' || !$this->package) {
            // $mdd = array_merge($mdd, [
            //     '_component' => $d . 'components.', // blade path to folder contains all of components
            //     '_template' => $d . 'templates.',
            //     '_pagination' => $d . 'pagination.',
            //     '_layout' => $d . 'layouts.',
            //     '_base' => $d,

            // ]);
        }
        $viewdata = array_merge($data, $mdd);
        return view($bp, $viewdata);
    }

    /**
     * giống view nhung trỏ sẵn vào module
     * @param string $bladeName
     * @param array $data dữ liệu truyền vào
     */
    public function renderModule($subModule, array $data = [])
    {
        return $this->render($this->moduleBlade . '.' . $subModule, $data);
    }

    public function renderPage($page, array $data = [])
    {
        return $this->render($this->pageViewBlade . '.' . $page, $data);
    }
}
