<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin_rights', function($user){
            return $user->isAdmin();
        });

        Gate::define('china_rights', function($user){
            return $user->isChinaAdmin();
        });

        Gate::define('phil_ind_rights', function($user){
            return $user->isPhilIndAdmin();
        });

        Gate::define('user_rights', function($user){
            return $user->isUser();
        });
    }
}
