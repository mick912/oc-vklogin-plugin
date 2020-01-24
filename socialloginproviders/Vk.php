<?php namespace Mirjan\Vklogin\SocialLoginProviders;

use Backend\Widgets\Form;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;
use Mirjan\Vklogin\Classes\VkProvider;
use Flynsarmy\SocialLogin\SocialLoginProviders\SocialLoginProviderBase;


class Vk extends SocialLoginProviderBase
{
	use \October\Rain\Support\Traits\Singleton;

	protected $driver = 'Vk';
    protected $adapter;
    protected $callback;
	/**
	 * Initialize the singleton free from constructor parameters.
	 */
	protected function init()
	{
		parent::init();
        $this->callback = URL::route('flynsarmy_sociallogin_provider_callback', ['Vk'], true);
	}

    public function getAdapter()
    {
        if ( !$this->adapter )
        {
            // Instantiate adapter using the configuration from our settings page
            $providers = $this->settings->get('providers', []);

            $this->adapter = new \Hybridauth\Provider\Vkontakte([
                'callback' => $this->callback,

                'keys' => [
                    'id'     => @$providers['Vk']['client_id'],
                    'secret' => @$providers['Vk']['client_secret'],
                    'public' => @$providers['Vk']['client_public'],
                ],

                'debug_mode' => config('app.debug', false),
                'debug_file' => storage_path('logs/flynsarmy.sociallogin.'.basename(__FILE__).'.log'),
            ]);
        }

        return $this->adapter;
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
        if ($this->getAdapter()->isConnected() )
            return \Redirect::to($this->callback);

        $this->getAdapter()->authenticate();
    }

    /**
     * Handles redirecting off to the login provider
     *
     * @return array ['token' => array $token, 'profile' => \Hybridauth\User\Profile]
     */
    public function handleProviderCallback()
    {
        $this->getAdapter()->authenticate();

        $token = $this->getAdapter()->getAccessToken();
        $profile = $this->getAdapter()->getUserProfile();

        // Don't cache anything or successive logins to different accounts
        // will keep logging in to the first account
        $this->getAdapter()->disconnect();

        return [
            'token' => $token,
            'profile' => $profile
        ];
    }
}
