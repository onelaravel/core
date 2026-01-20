<?php

namespace One\App\Providers;

use Illuminate\Support\ServiceProvider;
use One\App\Services\BladeCompilers\SubscribeDirectiveService;
use One\App\Services\BladeCompilers\CommonDirectiveService;
use One\App\Services\BladeCompilers\YieldDirectiveService;
use One\App\Services\BladeCompilers\WrapperDirectiveService;
use One\App\Services\BladeCompilers\ClientSideDirectiveService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use One\App\Services\BladeCompilers\SimplePhpStructureParserService;
use One\App\Services\BladeCompilers\EventDirectiveService;
use One\App\Services\BladeCompilers\AttrDirectiveService;
use One\App\Services\BladeCompilers\VarsDirectiveService;
use One\App\Services\BladeCompilers\LetConstDirectiveService;
use One\App\Services\BladeCompilers\TemplateDirectiveService;
use One\App\Services\BladeCompilers\ServerSideDirectiveService;
use One\App\Services\BladeCompilers\BlockDirectiveService;
use One\App\Services\BladeCompilers\SetupDirectiveService;
use One\App\Services\BladeCompilers\BindingDirectiveService;
use One\App\Services\BladeCompilers\PageDirectiveService;
use One\App\Services\BladeCompilers\OutDirectiveService;

class BladeDirectiveServiceProvider extends ServiceProvider
{
    /**
     * @var SimplePhpStructureParserService
     */
    protected $phpParser;
    /**
     * @var EventDirectiveService
     */
    protected $eventService;
    /**
     * @var VarsDirectiveService
     */
    protected $varsService;
    /**
     * @var LetConstDirectiveService
     */
    protected $letConstService;
    /**
     * @var TemplateDirectiveService
     */
    protected $templateService;
    /**
     * @var SubscribeDirectiveService
     */
    protected $subscribeService;
    /**
     * @var CommonDirectiveService
     */
    protected $commonService;
    /**
     * @var YieldDirectiveService
     */
    protected $yieldService;
    /**
     * @var WrapDirectiveService
     */
    protected $wrapService;
    /**
     * @var WrapperDirectiveService
     */
    protected $wrapperService;
    /**
     * @var ClientSideDirectiveService
     */
    protected $clientSideService;
    /**
     * @var ServerSideDirectiveService
     */
    protected $serverSideService;
    /**
     * @var BlockDirectiveService
     */
    protected $blockService;
    /**
     * @var SetupDirectiveService
     */
    protected $setupService;
    /**
     * @var BindingDirectiveService
     */
    protected $bindingService;
    /**
     * @var PageDirectiveService
     */
    protected $pageDirectiveService;
    /**
     * @var OutDirectiveService
     */
    protected $outService;
    /**
     * @var AttrDirectiveService
     */
    protected $attrService;
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(SimplePhpStructureParserService::class);
        $this->app->singleton(EventDirectiveService::class);
        $this->app->singleton(VarsDirectiveService::class);
        $this->app->singleton(AttrDirectiveService::class);
        
        // Initialize Blade compiler services
        $this->commonService = new CommonDirectiveService();
        $this->subscribeService = new SubscribeDirectiveService();
        $this->yieldService = new YieldDirectiveService($this->commonService);
        $this->wrapperService = new WrapperDirectiveService($this->commonService);
        $this->clientSideService = new ClientSideDirectiveService();
        $this->serverSideService = new ServerSideDirectiveService();
        $this->blockService = new BlockDirectiveService();
        $this->setupService = new SetupDirectiveService();
        $this->bindingService = new BindingDirectiveService();
        $this->pageDirectiveService = new PageDirectiveService();
        $this->outService = new OutDirectiveService();
        $this->app->singleton(LetConstDirectiveService::class);
        $this->app->singleton(TemplateDirectiveService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        app('view.finder')->addExtension('one');
        // Initialize services
        $this->phpParser = $this->app->make(SimplePhpStructureParserService::class);
        $this->eventService = $this->app->make(EventDirectiveService::class);
        $this->varsService = $this->app->make(VarsDirectiveService::class);
        $this->letConstService = $this->app->make(LetConstDirectiveService::class);
        $this->templateService = $this->app->make(TemplateDirectiveService::class);
        $this->registerDirectives();
        $this->attrService = $this->app->make(AttrDirectiveService::class);
        $this->attrService->registerDirectives();
        $this->wrapperService->registerDirectives();
        $this->letConstService->registerDirectives();
        $this->eventService->registerDirectives();
        $this->clientSideService->registerDirectives();
        $this->serverSideService->registerDirectives();
        $this->subscribeService->registerDirectives();

        $this->outService->registerDirectives();

        $this->yieldService->registerDirectives();
        $this->bindingService->registerDirectives();
        $this->registerScriptDirective();
        $this->registerResourcesDirective();
        $this->registerStylesDirective();
        $this->registerVueDirective();
        $this->registerRegisterDirective();
        $this->registerViewTypeDirective();
        $this->templateService->registerDirectives();
        $this->blockService->registerDirectives();

        $this->setupService->registerDirectives();
        $this->pageDirectiveService->registerDirectives();
    }

    /**
     * Đăng ký các Blade directive tùy chỉnh
     */
    protected function registerDirectives(): void
    {

        // Directive @await - await directive
        Blade::directive('await', function ($expression) {
            return "";
        });

        // Directive @vars - khai báo và kiểm tra biến
        Blade::directive('vars', function ($expression) {
            return $this->varsService->processVarsDirective($expression);
        });

        // Directive @viewId - tự động sinh UUID cho mỗi view
        Blade::directive('viewId', function ($expression) {
            return '<?php echo $__VIEW_ID__ ?? \Illuminate\Support\Str::uuid(); ?>';
        });


        // Directive @fetch - fetch directive
        Blade::directive('fetch', function ($expression) {
            return "";
        });

        // @attr handled by AttrDirectiveService
        // @checked and @selected are built-in Laravel directives

    }

    protected function registerViewTypeDirective(): void
    {
        Blade::directive('viewType', function ($expression) {
            return "<?php \$__VIEW_TYPE__ = \$__helper->registerViewType({$expression}) ?? (\$__VIEW_TYPE__ ?? 'view'); ?>";
        });
    }


    /**
     * Đăng ký OnInit directive
     */
    protected function registerOnInitDirective(): void
    {
        Blade::directive('oninit', function ($expression) {
            return '<?php \$__env->startSection("{$__VIEW_ID__}_oninit"); ?>';
        });

        Blade::directive('OnInit', function ($expression) {
            return '<?php \$__env->startSection("{$__VIEW_ID__}_oninit"); ?>';
        });

        Blade::directive('endoninit', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addOnInitCode(\$__env->yieldContent(\$__VIEW_ID__.'_oninit'), \$__VIEW_PATH__, \$__VIEW_ID__); ?>";
        });

        Blade::directive('endOnInit', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addOnInitCode(\$__env->yieldContent(\$__VIEW_ID__.'_oninit'), \$__VIEW_PATH__, \$__VIEW_ID__); ?>";
        });

        Blade::directive('endonInit', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addOnInitCode(\$__env->yieldContent(\$__VIEW_ID__.'_oninit'), \$__VIEW_PATH__, \$__VIEW_ID__); ?>";
        });

        Blade::directive('endInit', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addOnInitCode(\$__env->yieldContent(\$__VIEW_ID__.'_oninit'), \$__VIEW_PATH__, \$__VIEW_ID__); ?>";
        });
    }

    /**
     * Đăng ký Script directive
     */
    protected function registerScriptDirective(): void
    {
        Blade::directive('scripts', function ($expression) {
            return "<?php \$__env->startSection(\$__VIEW_ID__ . '_script'); ?>";
        });

        Blade::directive('endscripts', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addScript(\$__VIEW_ID__,\$__env->yieldContent(\$__VIEW_ID__.'_script')); ?>";
        });

        Blade::directive('endScripts', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addScript(\$__VIEW_ID__,\$__env->yieldContent(\$__VIEW_ID__.'_script')); ?>";
        });
    }

    /**
     * Đăng ký Resources directive
     */
    protected function registerResourcesDirective(): void
    {
        Blade::directive('resources', function ($expression) {
            return "<?php \$__env->startSection(\$__VIEW_ID__ . '_resources'); ?>";
        });

        Blade::directive('endresources', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addResources(\$__VIEW_ID__,\$__env->yieldContent(\$__VIEW_ID__.'_resources')); ?>";
        });

        Blade::directive('endResources', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addResources(\$__VIEW_ID__,\$__env->yieldContent(\$__VIEW_ID__.'_resources')); ?>";
        });
    }

    /**
     * Đăng ký Styles directive
     */
    protected function registerStylesDirective(): void
    {
        Blade::directive('styles', function ($expression) {
            return "<?php \$__env->startSection(\$__VIEW_ID__ . '_styles'); ?>";
        });

        Blade::directive('endstyles', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addStyles(\$__VIEW_ID__,\$__env->yieldContent(\$__VIEW_ID__.'_styles')); ?>";
        });

        Blade::directive('endStyles', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->addStyles(\$__VIEW_ID__,\$__env->yieldContent(\$__VIEW_ID__.'_styles')); ?>";
        });
    }

    /**
     * Đăng ký Vue directive
     */
    protected function registerVueDirective(): void
    {
        Blade::directive('vue', function ($expression) {
            return "<?php \$__env->startSection(\$__VIEW_ID__ . '_vue'); ?>";
        });

        Blade::directive('endvue', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->compileVue(\$__VIEW_ID__, \$__env->yieldContent(\$__VIEW_ID__.'_vue')); ?>";
        });
    }

    /**
     * Đăng ký Register directive
     */
    protected function registerRegisterDirective(): void
    {
        Blade::directive('register', function ($expression) {
            return "<?php \$__env->startSection(\$__VIEW_ID__.'_register'); ?>";
        });

        Blade::directive('Register', function ($expression) {
            return "<?php \$__env->startSection(\$__VIEW_ID__.'_register'); ?>";
        });

        Blade::directive('endregister', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->registerResources(\$__VIEW_ID__, \$__env->yieldContent(\$__VIEW_ID__.'_register')); ?>";
        });

        Blade::directive('endRegister', function ($expression) {
            return "<?php \$__env->stopSection(); \$__helper->registerResources(\$__VIEW_ID__, \$__env->yieldContent(\$__VIEW_ID__.'_register')); ?>";
        });
    }





    




}
