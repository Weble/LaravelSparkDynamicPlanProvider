<?php

namespace Webleit\LaravelSparkDynamicPlanProvider\Contracts;

use Carbon\Carbon;
use Laravel\Spark\Billable;

/**
 * Interface PlanObserverContract
 * @package Webleit\LaravelSparkDynamicPlanProvider\Contracts
 */
interface PlanObserverContract
{
    /**
     * @param PlanContract $plan
     */
    public function created (PlanContract $plan);


    /**
     * @param PlanContract $plan
     */
    public function updated (PlanContract $plan);
}