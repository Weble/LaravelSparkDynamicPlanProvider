<?php

namespace Webleit\LaravelSparkDynamicPlanProvider;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Spark\Billable;
use Webleit\LaravelSparkDynamicPlanProvider\Contracts\PlanContract;

/**
 * Class Plan
 * @package App
 */
class Plan extends Model implements PlanContract
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'provider_id';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = ['deleted_at' => 'datetime', 'features' => 'array'];

    /**
     * @param Carbon $from
     * @return \DateTimeZone|int|string
     */
    public function expiresAt (Carbon $from)
    {
        switch ($this->period) {
            case self::PERIOD_YEARLY:
                return $from->addYear();
                break;
            case self::PERIOD_MONTHLY:
            default:
                return $from->addMonth();
                break;
        }
    }

    /**
     * @param Billable $billable
     * @return float|int
     */
    public function priceForBillable($billable)
    {
        return $this->price * (1 + ( $billable->taxPercentage() / 100));
    }

    /**
     * @param Billable $billable
     * @return float|int
     */
    public function taxForBillable($billable)
    {
        return $this->price * $billable->taxPercentage() / 100;
    }

    /**
     * @return \Laravel\Spark\Plan
     */
    public function asSparkPlan()
    {
        return new \Laravel\Spark\Plan($this->name, $this->provider_id);
    }
}
