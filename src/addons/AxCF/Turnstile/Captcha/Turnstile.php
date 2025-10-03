<?php

namespace AxCF\Turnstile\Captcha;

use AxCF\Turnstile\Config;
use AxCF\Turnstile\Service\Verifier;
use XF\Captcha\AbstractCaptcha;

class Turnstile extends AbstractCaptcha
{
    public function isAvailable(): bool
    {
        $config = $this->getConfig();

        return $config['siteKey'] !== '' && $config['secretKey'] !== '';
    }

    protected function renderInternal(array $context): string
    {
        $config = $this->getConfig();

        if ($config['siteKey'] === '')
        {
            return '';
        }

        $params = [
            'siteKey' => $config['siteKey'],
            'theme' => $config['theme'],
            'size' => $config['size'],
            'context' => $context
        ];

        return $this->renderer('captcha_turnstile', $params);
    }

    public function isValid(): bool
    {
        $config = $this->getConfig();

        if ($config['siteKey'] === '' || $config['secretKey'] === '')
        {
            return false;
        }

        $token = $this->app->request()->filterSingle('cf-turnstile-response', 'str');
        if ($token === '')
        {
            return false;
        }

        $remoteIp = $this->app->request()->getIp();
        $result = $this->getVerifier()->verify($token, $config['secretKey'], $remoteIp);

        return $result->isSuccessful();
    }

    public function getTypeIconClass(): string
    {
        return 'fa-shield-halved';
    }

    protected function getConfig(): array
    {
        return $this->getConfigService()->toArray();
    }

    private function getConfigService(): Config
    {
        return new Config($this->app);
    }

    private function getVerifier(): Verifier
    {
        return new Verifier($this->app);
    }
}
