<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $couriers_arr = User::whereIn('role',['courier','agent'])->pluck('email')->toArray();
        $temp = [];
        for ($i=0; $i < count($couriers_arr); $i++) { 
            $t = explode('@', $couriers_arr[$i])[0];
            $temp[$t] = $t;
        }
        $couriers_arr = json_encode($temp);

        view()->composer('*',function($view) use ($couriers_arr){
            $view->with('couriers_arr', $couriers_arr);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
