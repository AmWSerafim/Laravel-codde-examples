<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use App\Models\Company;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            if(Auth::check()) {
                $user_id = Auth::user()->id;
                $user = User::find($user_id);
                $company = $user->company()->first();

                if ($company) {
                    $share_data['company_name'] = $company->title;
                } else {
                    $share_data['company_name'] = "";
                }
                $share_data['user_name'] = $user->name;

                if(!empty(Session::get('browsing_from'))){
                    $browsing_company = Company::find(Session::get('browsing_from'));
                    $share_data['browsing_from'] = $browsing_company->title;
                } else {
                    $share_data['browsing_from'] = "";
                }

                $view->with('share_data', $share_data);
            }
        });
    }
}
