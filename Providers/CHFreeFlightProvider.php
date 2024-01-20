<?php

namespace Modules\CHFreeFlight\Providers;

use App\Contracts\Modules\ServiceProvider;

/**
 * @package $NAMESPACE$
 */
class CHFreeFlightProvider extends ServiceProvider
{
    private $moduleSvc;

    protected $defer = false;

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->moduleSvc = app('App\Services\ModuleService');

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->registerLinks();

        // Uncomment this if you have migrations
        // $this->loadMigrationsFrom(__DIR__ . '/../$MIGRATIONS_PATH$');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }

    /**
     * Add module links here
     */
    public function registerLinks(): void
    {
        // Show this link if logged in
        $this->moduleSvc->addFrontendLink('Free Flight', '/chfreeflight', '', $logged_in=true);

        // Admin links:
        //$this->moduleSvc->addAdminLink('CHFreeFlight', '/admin/chfreeflight');
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('chfreeflight.php'),
        ], 'chfreeflight');

        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'chfreeflight');
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/chfreeflight');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([$sourcePath => $viewPath],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/chfreeflight';
        }, \Config::get('view.paths')), [$sourcePath]), 'chfreeflight');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/chfreeflight');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'chfreeflight');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'chfreeflight');
        }
    }
}
