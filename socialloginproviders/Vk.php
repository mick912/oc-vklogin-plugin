<?php namespace Mirjan\Vklogin\SocialLoginProviders;

use Backend\Widgets\Form;
use Mirjan\Vklogin\Classes\VkProvider;
use Flynsarmy\SocialLogin\SocialLoginProviders\SocialLoginProviderBase;
use Socialite;

use URL;

class Vk extends SocialLoginProviderBase
{
	use \October\Rain\Support\Traits\Singleton;

	protected $driver = 'Vk';

	/**
	 * Initialize the singleton free from constructor parameters.
	 */
	protected function init()
	{
		parent::init();

        // Socialite uses config files for credentials but we want to pass from
        // our settings page - so override the login method for this provider
        Socialite::extend($this->driver, /**
         *
         */
        function($app) {
            $providers = \Flynsarmy\SocialLogin\Models\Settings::instance()->get('providers', []);
            $providers['Vk']['redirect'] = URL::route('flynsarmy_sociallogin_provider_callback', ['Vk'], true);
            $provider = Socialite::buildProvider(
                VkProvider::class, (array)@$providers['Vk']
            );
            return $provider;
        });
	}

	public function isEnabled()
	{
		$providers = $this->settings->get('providers', []);

		return !empty($providers['Vk']['enabled']);
	}

    public function isEnabledForBackend()
    {
        $providers = $this->settings->get('providers', []);

        return !empty($providers['Vk']['enabledForBackend']);
    }

	public function extendSettingsForm(Form $form)
	{
		$form->addFields([
			'noop' => [
				'type' => 'partial',
				'path' => '$/mirjan/vklogin/partials/backend/forms/settings/_vk_info.htm',
				'tab' => 'Vk',
			],

			'providers[Vk][enabled]' => [
                'label' => 'Enabled on frontend?',
				'type' => 'checkbox',
                'comment' => 'Can frontend users log in with Vk?',
                'default' => 'true',
                'span' => 'left',
				'tab' => 'Vk',
			],

            'providers[Vk][enabledForBackend]' => [
                'label' => 'Enabled on backend?',
                'type' => 'checkbox',
                'comment' => 'Can administrators log into the backend with Vk?',
                'default' => 'false',
                'span' => 'right',
                'tab' => 'Vk',
            ],

			'providers[Vk][client_id]' => [
				'label' => 'Application ID',
				'type' => 'text',
				'tab' => 'Vk',
			],

			'providers[Vk][client_public]' => [
				'label' => 'Public Key',
				'type' => 'text',
				'tab' => 'Vk',
			],
            'providers[Vk][client_secret]' => [
				'label' => 'Secure key',
				'type' => 'text',
				'tab' => 'Vk',
			],
		], 'primary');
	}

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider()
    {
        return Socialite::driver($this->driver)->scopes(['email'])->redirect();
    }

    /**
     * Handles redirecting off to the login provider
     *
     * @return array
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver($this->driver)->user();

        return (array)$user;
    }
}