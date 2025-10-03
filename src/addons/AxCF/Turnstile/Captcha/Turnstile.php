<?php

namespace AxCF\Turnstile\Captcha;

use XF\Captcha\AbstractCaptcha;
use XF\Util\Json;
use function in_array;
use function is_array;
use function json_encode;

class Turnstile extends AbstractCaptcha
{
    protected const VERIFY_ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    protected const REQUEST_TIMEOUT = 5.0;

    public function isAvailable(): bool
    {
        $config = $this->getConfig();

        return !empty($config['siteKey']) && !empty($config['secretKey']);
    }

    protected function renderInternal(array $context): string
    {
        $config = $this->getConfig();

        if (empty($config['siteKey']))
        {
            return '';
        }

        $theme = $config['theme'] ?? 'auto';
        if (!in_array($theme, ['auto', 'light', 'dark'], true))
        {
            $theme = 'auto';
        }

        $size = $config['size'] ?? 'normal';
        if (!in_array($size, ['normal', 'compact'], true))
        {
            $size = 'normal';
        }

        $params = [
            'siteKey' => $config['siteKey'],
            'theme' => $theme,
            'size' => $size,
            'context' => $context
        ];

        return $this->renderer('captcha_turnstile', $params);
    }

    public function isValid(): bool
    {
        $config = $this->getConfig();

        if (empty($config['siteKey']) || empty($config['secretKey']))
        {
            return false;
        }

        $request = $this->app->request();
        $token = $request->filterSingle('cf-turnstile-response', 'str');

        if ($token === '')
        {
            return false;
        }

        $formParams = [
            'secret' => $config['secretKey'],
            'response' => $token
        ];

        $remoteIp = $request->getIp();
        if ($remoteIp)
        {
            $formParams['remoteip'] = $remoteIp;
        }

        $client = $this->app->http()->client();

        try
        {
            $response = $client->post(self::VERIFY_ENDPOINT, [
                'form_params' => $formParams,
                'timeout' => self::REQUEST_TIMEOUT
            ]);
        }
        catch (\Throwable $e)
        {
            \XF::logException($e, false, '[AxCF Turnstile] Verification request failed: ');
            return false;
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200)
        {
            \XF::logError('[AxCF Turnstile] Unexpected verification status code: ' . $statusCode);
            return false;
        }

        $responseBody = (string) $response->getBody();

        try
        {
            $data = Json::decode($responseBody, true);
        }
        catch (\Throwable $e)
        {
            \XF::logException($e, false, '[AxCF Turnstile] Invalid verification response: ');
            return false;
        }

        if (!is_array($data))
        {
            return false;
        }

        if (!empty($data['success']))
        {
            return true;
        }

        if (!empty($data['error-codes']))
        {
            $message = '[AxCF Turnstile] Verification failed: ' . json_encode($data['error-codes']);
            \XF::logError($message);
        }

        return false;
    }

    public function getTypeIconClass(): string
    {
        return 'fa-shield-halved';
    }

    protected function getConfig(): array
    {
        $config = $this->app->config()['axcfTurnstile'] ?? [];

        return is_array($config) ? $config : [];
    }
}
