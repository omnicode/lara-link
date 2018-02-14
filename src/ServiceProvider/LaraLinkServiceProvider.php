<?php

namespace LaraLink\ServiceProvider;

use LaraForm\ServiceProvider\LaraFormServiceProvider;
use LaraLink\Components\LinkRoute;
use LaraLink\Facades\LaraLink;
use LaraLink\LinkBuilder;
use LaraLink\Links\ItemActionLink;
use LaraSupport\LaraServiceProvider;

class LaraLinkServiceProvider extends LaraServiceProvider
{

    /**
     *
     */
    public function boot()
    {
        $this->mergeConfig(__DIR__);
    }

    /**
     *
     */
    public function register()
    {
        $this->registerProviders(LaraFormServiceProvider::class);
        $this->registerAlias('LaraLink', LaraLink::class);
        $this->registerSingletons([
            'laralink.components.route'=> LinkRoute::class,
            'laralink.links.item-action' => ItemActionLink::class,
            'laralink' => LinkBuilder::class
        ]);
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
