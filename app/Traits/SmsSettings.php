<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 24/05/17
 * Time: 11:29 PM
 */

namespace App\Traits;

// use App\CompanySetting;
// use App\GlobalSetting;
use App\SmsSetting;
use Illuminate\Notifications\VonageChannelServiceProvider;
use Illuminate\Support\Facades\Config;

trait SmsSettings{

    public function setSmsConfigs(){
        // $settings = GlobalSetting::first();
        
        // $company = explode(' ',trim($settings->company_name));
        
        $smsSetting = SmsSetting::first();

        if ($smsSetting && \config('app.env') !== 'development') {
            Config::set('vonage.api_key', $smsSetting->nexmo_key);
            Config::set('vonage.api_secret', $smsSetting->nexmo_secret);
            Config::set('vonage.sms_from', $smsSetting->nexmo_from);
        }

        (new VonageChannelServiceProvider(app()))->register();
    }
}


