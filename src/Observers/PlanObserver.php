<?php

namespace Webleit\LaravelSparkDynamicPlanProvider;

use App\Plan;
use Laravel\Cashier\Cashier;

/**
 * Class PlanObserver
 * @package App\Observers
 */
class PlanObserver
{
    /**
     * @param Plan $plan
     */
    public function created (Plan $plan)
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
     * @param Plan $plan
     */
    public function updated (Plan $plan)
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
     * @param Plan $plan
     */
    public function deleting (Plan $plan)
    {
        // Dont' delete it, keep it for archiving
    }
}