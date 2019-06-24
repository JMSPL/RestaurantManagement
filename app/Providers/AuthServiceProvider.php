<?php

namespace App\Providers;

use App\Invoice;
use App\Meal;
use App\Policies\InvoicePolicy;
use App\Policies\MealPolicy;
use App\Policies\UserPolicy;
use App\Policies\WorkerPolicy;
use App\User;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Worker::class => WorkerPolicy::class,
        Meal::class => MealPolicy::class,
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addDays(7));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(14));
    }
}
