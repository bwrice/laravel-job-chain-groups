<?php

namespace Bwrice\LaravelJobChainGroups;

use Bwrice\LaravelJobChainGroups\Services\JobChainGroups;
use Illuminate\Support\ServiceProvider;

class JobChainGroupsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-job-chain-groups');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-job-chain-groups');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-job-chain-groups.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-job-chain-groups'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-job-chain-groups'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-job-chain-groups'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }

        if (! class_exists('CreateChainGroupsTable') && ! class_exists('CreateChainGroupMembersTable')) {
            $this->publishes([
                __DIR__.'/../stubs/create_chain_groups_table.stub.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_chain_groups_table.php'),
                __DIR__.'/../stubs/create_chain_group_members_table.stub.php' => database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_chain_group_members_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
//        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-job-chain-groups');
//
        // Register the main class to use with the facade
        $this->app->singleton('job-chain-groups', function () {
            return new JobChainGroups;
        });
    }
}
