<?php

namespace Webleit\LaravelSparkDynamicPlanProvider\Observers;

use Laravel\Cashier\Cashier;
use Webleit\LaravelSparkDynamicPlanProvider\Contracts\PlanContract;
use Webleit\LaravelSparkDynamicPlanProvider\Contracts\PlanObserverContract;

/**
 * Class PlanObserver
 * @package App\Observers
 */
class PlanObserver implements PlanObserverContract
{
    /**
     * @param PlanContract $plan
     */
    public function created (PlanContract $plan)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        \Stripe\Plan::create([
            'id' => $plan->provider_id,
            'amount' => $plan->price * 100,
            'interval' => ($plan->period == Plan::PERIOD_MONTHLY ? Plan::STRIPE_PERIOD_MONTHLY : Plan::STRIPE_PERIOD_YEARLY),
            'name' => $plan->name,
            'currency' => Cashier::usesCurrency()
        ]);
    }

    /**
     * @param PlanContract $plan
     */
    public function updated (PlanContract $plan)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        \Stripe\Plan::update($plan->provider_id, [
            'amount' => $plan->price * 100,
            'interval' => ($plan->period == Plan::PERIOD_MONTHLY ? Plan::STRIPE_PERIOD_MONTHLY : Plan::STRIPE_PERIOD_YEARLY),
            'name' => $plan->name,
            'currency' => Cashier::usesCurrency()
        ]);
    }

    /**
     * @param PlanContract $plan
     */
    public function deleting (PlanContract $plan)
    {
        // Dont' delete it, keep it for archiving
    }
}