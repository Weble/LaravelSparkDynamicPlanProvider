<?php

namespace Webleit\LaravelSparkDynamicPlanProvider;

use App\Observers\PlanObserver;
use App\Plan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

/**
 * Class ZohoBooksServiceProvider
 * @package App\Providers
 */
class DynamicPlanServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishMigrations();
        $this->publishConfig();

        Plan::observe(PlanObserver::class);

        if (config('dynamicplans.autoload', true)) {
            $this->registerSparkPlans(
                $this->loadPlans()
            );
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function loadPlans()
    {
        if (config('dynamicplans.cache', null) === null) {
            return Plan::all();
        }

        return Cache::store(config('dynamicplans.cache'))->remember('dynamicplans.plans', function (){
            return Plan::all();
        });
    }

    /**
     * @param $plans
     */
    public function registerSparkPlans($plans)
    {
        // Create a Plan for each plan in the db
        $plans->each(function($plan){
            $sparkPlan = Spark::plan($plan->name, $plan->provider_id)
                ->price($plan->price)
                ->features($plan->features ?: []);

            if ($plan->period == Plan::PERIOD_YEARLY) {
                $sparkPlan->yearly();
            }
        });
    }

    public function register ()
    {
        $this->mergeConfigFrom(__DIR__.'/config/dynamicplans.php', 'dynamicplans');
    }

    protected function publishMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations')
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
