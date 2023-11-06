<?php

namespace App\Providers;

use App\Models\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class HospitalComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public $access_token;
    public static $fb_access_token;

    public function register(): void
    {
        view()->composer('*', function ($view) {

            if(Auth::user()){
                $hospital = Hospital::find(Auth::user()->hospital_id);
            }else{
                $hospital = [];
            }

            // Sharing the $hospital variable to all views
            $view->with('hospital', $hospital);
            // $main_cols = json_decode(json_encode($hospital->main_cols),true);
            // $this->access_token = $main_cols['access_token'];
            // Self::$fb_access_token = $this->access_token;
            // info('viewing access token '.Self::$fb_access_token);
            // return $this->access_token;
        });
    }

    // public static function getAccessToken(){
    //     return 'Hello';
    // }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
