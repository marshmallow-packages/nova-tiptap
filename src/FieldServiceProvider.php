<?php

namespace Marshmallow\Tiptap;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang');

        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../config/nova-tiptap.php' => config_path('nova-tiptap.php'),
        ], 'nova-tiptap-config');

        Nova::serving(function (ServingNova $event) {
            Nova::provideToScript([
                'tiptapTranslations' => $this->translations(),
            ]);

            Nova::script('tiptap', __DIR__ . '/../dist/js/field.js');
            Nova::style('tiptap', __DIR__ . '/../dist/css/tiptap.css');
        });
    }

    /**
     * Register routes.
     *
     * @return void
     */
    protected function routes()
    {
        Route::middleware(['nova'])
            ->prefix('nova-tiptap/api')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/nova-tiptap.php', 'nova-tiptap');

        $this->app->bind('tiptap-content-blocks', function () {
            return new TiptapContentBlocks();
        });
    }

    private function translations()
    {
        $file = resource_path('lang/vendor/nova-tiptap/' . app()->getLocale() . '.json');

        if (! is_readable($file)) {
            $file = base_path('vendor/marshmallow/nova-tiptap/resources/lang/' . app()->getLocale() . '.json');
        }

        if (is_readable($file)) {
            $json = json_decode(file_get_contents($file));

            return is_object($json) ? $json : [];
        }

        return [];
    }
}
