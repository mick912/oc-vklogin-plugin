<?php namespace Mirjan\Vklogin;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.User', 'Flynsarmy.SocialLogin'];

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }



    public function  register_flynsarmy_sociallogin_providers()
    {
        return [
            '\\Mirjan\\Vklogin\\SocialLoginProviders\\Vk' => [
                'label' => 'Vk',
                'alias' => 'Vk',
                'description' => 'Log in with vk.com'
            ],
        ];
    }
}
