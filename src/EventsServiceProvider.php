<?php

namespace Laralum\Events;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laralum\Events\Models\Event;
use Laralum\Events\Models\Settings;
use Laralum\Events\Policies\EventPolicy;
use Laralum\Blog\Policies\SettingsPolicy;
use Laralum\Permissions\PermissionsChecker;

class EventsServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Event::class    => EventPolicy::class,
        Settings::class => SettingsPolicy::class,
    ];

    /**
     * The mandatory permissions for the module.
     *
     * @var array
     */
    protected $permissions = [
        [
            'name' => 'Events Access',
            'slug' => 'laralum::events.access',
            'desc' => 'Grants access to events',
        ],
        [
            'name' => 'Create Events',
            'slug' => 'laralum::events.create',
            'desc' => 'Allows creating events',
        ],
        [
            'name' => 'Update Events Categories',
            'slug' => 'laralum::events.update',
            'desc' => 'Allows updating events',
        ],
        [
            'name' => 'View Events Categories',
            'slug' => 'laralum::events.categories.view',
            'desc' => 'Allows view events',
        ],
        [
            'name' => 'Delete Events',
            'slug' => 'laralum::events.delete',
            'desc' => 'Allows delete events',
        ],
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->publishes([
            __DIR__.'/Views/public' => resource_path('views/vendor/laralum_events/public'),
        ], 'laralum_events');

        $this->loadViewsFrom(__DIR__.'/Views', 'laralum_events');

        $this->loadTranslationsFrom(__DIR__.'/Translations', 'laralum_events');

        if (!$this->app->routesAreCached()) {
            require __DIR__.'/Routes/web.php';
        }

        $this->app->register('GrahamCampbell\\Markdown\\MarkdownServiceProvider');

        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        // Make sure the permissions are OK
        PermissionsChecker::check($this->permissions);
    }

    /**
     * I cheated this comes from the AuthServiceProvider extended by the App\Providers\AuthServiceProvider.
     *
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}