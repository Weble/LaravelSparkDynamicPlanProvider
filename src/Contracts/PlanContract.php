<?php

namespace Webleit\LaravelSparkDynamicPlanProvider\Contracts;

use Carbon\Carbon;
use Laravel\Spark\Billable;

/**
 * Interface PlanContract
 * @package Webleit\LaravelSparkDynamicPlanProvider\Contracts
 */
interface PlanContract
{
    /**
     * Periods
     */
    const PERIOD_MONTHLY = 'monthly';
    const PERIOD_YEARLY = 'yearly';

    /**
     * Stripe Periods
     */
    const STRIPE_PERIOD_MONTHLY = 'month';
    const STRIPE_PERIOD_YEARLY = 'year';
    
    /**
     * @param Carbon $from
     * @return \DateTimeZone|int|string
     */
    public function expiresAt (Carbon $from);


    /**
     * @param Billable $billable
     * @return float|int
     */
    public function priceForBillable($billable);

    /**
     * @param Billable $billable
     * @return float|int
     */
    public function taxForBillable($billable);


    /**
     * @return \Laravel\Spark\Plan
     */
    public function asSparkPlan();
}