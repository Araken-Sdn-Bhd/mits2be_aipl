<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\EmailSetting;
use Illuminate\Support\Facades\Config;

class MailConfigProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $emailSetting = EmailSetting::get()->toArray();
        //  dd($emailSetting);
        $configuration = $emailSetting[0];
        if (!is_null($configuration)) {
            $config = array(
                'driver'     =>     'smtp',
                'host'       =>     $configuration['outgoing_smtp_server'],
                'port'       =>     $configuration['smtp_port_number'],
                'username'   =>     $configuration['login_user_id'],
                'password'   =>     $configuration['login_password'],
                'encryption' =>     $configuration['security'],
                'from'       =>     array('address' => $configuration['send_email_from'], 'name' => 'Mentari'),
            );
            Config::set('mail', $config);
        }
    }
}
