# LaravelSparkDynamicPlanProvider

Laravel Spark Service Provider that allows to create Plans with Eloquent and have them created on Stripe, automatically loading them into Spark.

# Installation

```composer require webleit/laravel-spark-dynamic-plan-provider```

# Usage

The Service Provider will autoregister itself into laravel.

After that, just create a Plan as you do with any Eloquent model, and it will be created into Stripe first (you need to have your stripe api key set into the `services` configuration file), then loaded into Spark.

```php

<?php

...

use Webleit\LaravelSparkDynamicPlanProvider\Plan;

...

Plan::create([
  'name' => 'My test plan name',
  'provider_id' => 'stripe-plan-id',
  'description' => 'test description',
  'price' => 9.99, // Tax excluded, it will apply taxes automatically using your Spark config
  'trial' => 30, // Days of trial
  'period' => Plan::PERIOD_MONTHLY, // or Plan::PERIOD_YEARLY
  'features' => [
    'feature 1',
    'feature 2'
  ]
]);

```

You can than use that as any other eloquent model.
You can always retrieve Spark's plan using ```$plan->asSparkPlan()```

# Config

```php
[
    /**
     * Cache Store to use for caching Spark plans.
     * Null disables the caching
     */
    'cache' => null,

    /**
     * Should the provider autoconfigure the Spark plans on boot?
     */
    'autoload' => true

]
```
