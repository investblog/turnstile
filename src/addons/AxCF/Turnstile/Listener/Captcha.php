<?php

namespace AxCF\Turnstile\Listener;

use XF\App;

class Captcha
{
    public static function captchaTypes(App $app, array &$types): void
    {
        $types['axcfTurnstile'] = [
            'name' => 'Cloudflare Turnstile',
            'title' => 'Cloudflare Turnstile',
            'description' => 'Cloudflare Turnstile challenge provided by Cloudflare.',
            'class' => 'AxCF\\Turnstile\\Captcha\\Turnstile',
            'icon' => 'fa-shield-halved'
        ];
    }
}
