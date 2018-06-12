<?php

namespace Todotix\Customer;

use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function boot() {
        /* Publicar Elementos */
        $this->publishes([
            __DIR__ . '/config' => config_path()
        ], 'config');

        /* Cargar Traducciones */
        $this->loadTranslationsFrom(__DIR__.'/lang', 'customer');

        /* Cargar Vistas */
        $this->loadViewsFrom(__DIR__ . '/views', 'customer');
    }


    public function register() {
        /* Registrar ServiceProvider Internos */
        //$this->app->register('Vinkla\Pusher\PusherServiceProvider');

        /* Registrar Alias */
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Customer', '\Todotix\Customer\App\Helpers\Customer');

        /* Comandos de Consola */
        $this->commands([
            \Todotix\Customer\App\Console\GeneralTest::class,
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/config/customer.php', 'customer'
        );
    }
    
}
