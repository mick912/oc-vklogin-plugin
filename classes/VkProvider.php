<?php
namespace Mirjan\Vklogin\Classes;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\User;
use SocialiteProviders\VKontakte\Provider;

class VkProvider extends Provider
{
       /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => Arr::get($user, 'id'),
            'nickname' => Arr::get($user, 'screen_name'),
            'name'     => trim(Arr::get($user, 'first_name').' '.Arr::get($user, 'last_name')),
            'email'    => array_get($user, 'email', 'vk' . array_get($user, 'id') . '@' . array_get($user, 'id') . '.com'),
            'avatar'   => Arr::get($user, 'photo'),
            'avatar_original'   => Arr::get($user, 'photo'),
        ]);
    }

}
