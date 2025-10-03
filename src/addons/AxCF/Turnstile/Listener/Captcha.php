<?php

namespace AxCF\Turnstile\Listener;

use XF\App;

class Captcha
{
    public static function captchaTypes(App $app, array &$types): void
    {
        $phrase = fn (string $name) => $app->phrase($name);

        $types['axcfTurnstile'] = [
            'name' => $phrase('axcf_turnstile_captcha_title'),
            'title' => $phrase('axcf_turnstile_captcha_title'),
            'description' => $phrase('axcf_turnstile_captcha_description'),
            'class' => 'AxCF\\Turnstile\\Captcha\\Turnstile',
            'icon' => 'fa-shield-halved'
        ];
    }
}
