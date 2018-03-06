<?php

namespace Webleit\LaravelSparkDynamicPlanProvider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Webleit\LaravelSparkDynamicPlanProvider\Contracts\PlanContract;
use Webleit\LaravelSparkDynamicPlanProvider\Contracts\PlanObserverContract;
use Webleit\LaravelSparkDynamicPlanProvider\Observers\PlanObserver;
use Laravel\Spark\Spark;

/**
 * Class DynamicPlanServiceProvider
 * @package App\Providers
 */
class DynamicPlanServiceProvider extends ServiceProvider
{
    public $bindings = [
        PlanContract::class => Plan::class,
        PlanObserverContract::class => PlanObserver::class
    ];

    public function boot ()
    {
        $this->publishMigrations();
        $this->publishConfig();

        $this->observePlans();

        if (config('dynamicplans.autoload', true)) {
            $this->registerSparkPlans(
                $this->loadPlans()
            );
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function loadPlans ()
    {
        if (config('dynamicplans.cache', null) === null) {
            return app(PlanContract::class)->all();
        }

        return Cache::store(config('dynamicplans.cache'))->remember('dynamicplans.plans', function () {
            return app(PlanContract::class)->all();
        });
    }

    /**
     * @param $plans
     */
    public function registerSparkPlans ($plans)
    {
        // Create a Plan for each plan in the db
        $plans->each(function ($plan) {
            $sparkPlan = Spark::plan($plan->name, $plan->provider_id)
                ->price($plan->price)
                ->features($plan->features ?: []);

            if ($plan->period == Plan::PERIOD_YEARLY) {
                $sparkPlan->yearly();
            }
        });
    }

    protected function observePlans ()
    {
        $className = get_class(app(PlanContract::class));
        call_user_func([$className, 'observe'], PlanObserver::class);
    }

    public function register ()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/dynamicplans.php', 'dynamicplans');
    }

    protected function publishMigrations ()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations')
        ], 'migrations');
    }

    protected function publishConfig (): void
    {
        // Publish a config file
        $this->publishes([
            __DIR__ . '/config/dynamicplans.php' => config_path('dynamicplans.php'),
        ], 'config');
    }
}
