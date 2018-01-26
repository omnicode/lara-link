<?php
namespace LaraLink\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use LaraLink\Components\LinkRoute;
use LaraLink\LinkBuilder;
use LaraLink\Links\ItemActionLink;

class LaraLinkServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $configPath = __DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
            .'config' . DIRECTORY_SEPARATOR . 'lara_link.php';
        $this->mergeConfigFrom($configPath, 'lara_link');
    }

    /**
     *
     */
    public function register()
    {
        $this->registerLinkComponents();
        $this->registerLinks();
        $this->registerLinkBuilder();
    }

    /**
     *
     */
    protected function registerLinkComponents()
    {
        $this->app->singleton('laralink.components.route', function ($app) {
            return new LinkRoute();
        });

    }

    /**
     *
     */
    protected function registerLinks()
    {
        $this->app->singleton('laralink.links.item-action', function ($app) {
            return new ItemActionLink(
                $app['laralink.components.route']
            );
        });

    }

    /**
     *
     */
    protected function registerlinkBuilder()
    {
        $this->app->singleton('laralink', function ($app) {
            return new LinkBuilder(
                $app['laralink.components.route'],
                $app['laralink.links.item-action']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laralink'];
    }
}
