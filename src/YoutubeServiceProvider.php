<?php

namespace SdSmith1981\Youtube;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class YoutubeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLegacyLaravel() || $this->isOldLaravel()) {
            $this->package('sdsmith1981/youtube', 'sdsmith1981/youtube');
        }

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Youtube', 'SdSmith1981\Youtube\Facades\Youtube');
        $this->publishes(array(__DIR__ . '/../../config/youtube.php' => config_path('youtube.php')));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->isLegacyLaravel() || $this->isOldLaravel()) {
            $this->app['youtube'] = $this->app->share(function ($app) {
                $key = \Config::get('sdsmith1981/youtube::KEY');
                return new Youtube($key);
            });

            return;
        }

        if (floatval(Application::VERSION) >= 5.4) {
            $this->app->bind("youtube", function(){
                return new Youtube(config('youtube.KEY'));
            });
        } elseif(floatval(Application::VERSION) >= 5.1) { //Laravel 5.1+ fix
            $this->app->bind("youtube", function(){
                return $this->app->make('SdSmith1981\Youtube\Youtube', [config('youtube.KEY')]);
            });
        }else{
            $this->app->bindShared('youtube', function () {
                return $this->app->make('SdSmith1981\Youtube\Youtube', [config('youtube.KEY')]);
            });
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('youtube');
    }

    public function isLegacyLaravel()
    {
        return Str::startsWith(Application::VERSION, array('4.1.', '4.2.'));
    }

    public function isOldLaravel()
    {
        return Str::startsWith(Application::VERSION, '4.0.');
    }
}
