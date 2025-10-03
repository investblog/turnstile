<?php

namespace AxCF\Turnstile\Service;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use XF\App;
use XF\Util\Json;
use function is_array;
use function is_string;
use function json_encode;

class Verifier
{
    private const VERIFY_ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    private const REQUEST_TIMEOUT = 5.0;

    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function verify(string $token, string $secretKey, ?string $remoteIp = null): VerificationResult
    {
        if ($token === '' || $secretKey === '')
        {
            return VerificationResult::failure();
        }

        $formParams = [
            'secret' => $secretKey,
            'response' => $token
        ];

        if ($remoteIp)
        {
            $formParams['remoteip'] = $remoteIp;
        }

        try
        {
            $response = $this->app->http()->client()->post(self::VERIFY_ENDPOINT, [
                'form_params' => $formParams,
                'timeout' => self::REQUEST_TIMEOUT
            ]);
        }
        catch (GuzzleException $e)
        {
            \XF::logException($e, false, '[AxCF Turnstile] Verification request failed: ');
            return VerificationResult::failure(['request_failed']);
        }
        catch (\Throwable $e)
        {
            \XF::logException($e, false, '[AxCF Turnstile] Verification threw: ');
            return VerificationResult::failure(['request_failed']);
        }

        return $this->parseResponse($response);
    }

    private function parseResponse(ResponseInterface $response): VerificationResult
    {
        if ($response->getStatusCode() !== 200)
        {
            \XF::logError('[AxCF Turnstile] Unexpected verification status code: ' . $response->getStatusCode());
            return VerificationResult::failure(['http_error']);
        }

        $body = (string) $response->getBody();

        try
        {
            $payload = Json::decode($body, true);
        }
        catch (\Throwable $e)
        {
            \XF::logException($e, false, '[AxCF Turnstile] Invalid verification response: ');
            return VerificationResult::failure(['invalid_response']);
        }

        if (!is_array($payload))
        {
            return VerificationResult::failure(['invalid_payload']);
        }

        if (!empty($payload['success']))
        {
            return VerificationResult::success();
        }

        $errors = [];
        if (!empty($payload['error-codes']) && is_array($payload['error-codes']))
        {
            foreach ($payload['error-codes'] as $error)
            {
                if (is_string($error) && $error !== '')
                {
                    $errors[] = $error;
                }
            }
        }

        if ($errors)
        {
            \XF::logError('[AxCF Turnstile] Verification failed: ' . json_encode($errors));
        }

        return VerificationResult::failure($errors);
    }
}
