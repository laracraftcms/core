<?php

namespace Laracraft\Core\Providers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\ServiceProvider;
use Laracraft\Core\Database\Connections\MysqlConnection;
use Laracraft\Core\Database\Connections\PostgresConnection;
use Laracraft\Core\Database\Connections\SQLiteConnection;
use Laracraft\Core\Database\Connections\SqlServerConnection;
use Laracraft\Core\Database\Generators\FieldGroupTable;
use Laracraft\Core\Entities\Entry;
use Laracraft\Core\Entities\Helpers\FieldManger;
use Laracraft\Core\Entities\Helpers\UrlFormatter;
use Laracraft\Core\Entities\Helpers\ViewFormatter;
use Laracraft\Core\Http\Helpers\Assets\Facades\Script;
use Laracraft\Core\Http\Helpers\Assets\Facades\Style;
use Laracraft\Core\Http\Helpers\Assets\StyleManager;
use Laracraft\Core\Http\Helpers\Assets\ScriptManager;
use Laracraft\Core\Console\GenerateLaracraftJSRoutes;
use Laracraft\Core\Entities\EditLock;
use Laracraft\Core\Entities\Section;
use Laracraft\Core\Entities\EntryType;
use Laracraft\Core\Policies\EditLockPolicy;
use Laracraft\Core\Policies\SectionPolicy;
use Laracraft\Core\Policies\EntryTypePolicy;
use Laracraft\Core\Policies\EntryPolicy;
use Laracraft\Core\Repositories\EntryTypeRepository;
use SuperClosure\Analyzer\TokenAnalyzer;
use SuperClosure\Serializer;
use Laracraft\Breadcrumbs\Generator;
use Laracraft\Breadcrumbs\Manager;
use Laracraft\Core\Repositories\Contracts\SectionRepositoryContract;
use Laracraft\Core\Repositories\SectionRepository;
use Laracraft\Core\Repositories\Contracts\EntryTypeRepositoryContract;
use Laracraft\Core\Repositories\FieldLayoutRepository;
use Laracraft\Core\Repositories\Contracts\FieldLayoutRepositoryContract;
use Laracraft\Core\Repositories\FieldGroupRepository;
use Laracraft\Core\Repositories\Contracts\FieldGroupRepositoryContract;
use Laracraft\Core\Repositories\FieldRepository;
use Laracraft\Core\Repositories\Contracts\FieldRepositoryContract;
use Laracraft\Core\Repositories\UrlRepository;
use Laracraft\Core\Repositories\Contracts\UrlRepositoryContract;
use Laracraft\Core\Repositories\EntryRepository;
use Laracraft\Core\Repositories\Contracts\EntryRepositoryContract;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

	protected $policies = [
		EditLock::class => EditLockPolicy::class,
		Section::class => SectionPolicy::class,
		EntryType::class => EntryTypePolicy::class,
		Entry::class => EntryPolicy::class
	];

	/**
	 * Boot the application events.
	 *
	 * @param GateContract $gate
	 */
    public function boot(GateContract $gate)
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
		$this->registerCommands();
		$this->registerPolicies($gate);
    }

	public function registerCommands(){
		$this->commands([
			GenerateLaracraftJSRoutes::class
						]);
	}

	public function registerPolicies(GateContract $gate)
	{
		foreach ($this->policies as $key => $value) {
			$gate->policy($key, $value);
		}
	}

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

		//Script + Style Asset managers
		$this->app->singleton('StyleManager', function () {
			return new StyleManager();
		});
		$this->app->singleton('ScriptManager', function () {
			return new ScriptManager();
		});

		//db connections
		$this->app->bind('db.connection.mysql', MysqlConnection::class);
		$this->app->bind('db.connection.pgsql', PostgresConnection::class);
		$this->app->bind('db.connection.sqlite', SQLiteConnection::class);
		$this->app->bind('db.connection.sqlsrv', SqlServerConnection::class);

		//closure capable serializer
		$this->app->singleton('serializer', function(){
			return new Serializer(new TokenAnalyzer());
		});

		//breadcrumbs
		$this->app->bind(Generator::class, function ($app) {
			return new Generator(new Container(), '');
		});

		$this->app->singleton('laracraft-breadcrumbs', function($app)
		{
			$breadcrumbs = $this->app->make(Manager::class);

			$breadcrumbs->setView('core::cp.components.breadcrumbs');

			return $breadcrumbs;
		});

		//migration generators
		$this->app->bind('laracraft.generator.migration.field_group', FieldGroupTable::class);

		//repositories
		$this->app->bind(SectionRepositoryContract::class, SectionRepository::class);
		$this->app->bind(EntryTypeRepositoryContract::class, EntryTypeRepository::class);
		$this->app->bind(FieldLayoutRepositoryContract::class, FieldLayoutRepository::class);
		$this->app->bind(FieldGroupRepositoryContract::class, FieldGroupRepository::class);
		$this->app->bind(FieldRepositoryContract::class, FieldRepository::class);
		$this->app->bind(UrlRepositoryContract::class, UrlRepository::class);
		$this->app->bind(EntryRepositoryContract::class, EntryRepository::class);

		//helpers
		$this->app->singleton('laracraft.urlformatter', UrlFormatter::class);
		$this->app->singleton('laracraft.viewformatter', ViewFormatter::class);
		$this->app->singleton('laracraft.fieldmanager', FieldManger::class);
	}

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/laracraft-core.php' => config_path('laracraft-core.php'),
        ],'config');
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/core');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/core';
        }, config('view.paths')), [$sourcePath]), 'core');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/core');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'core');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'core');
        }
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
