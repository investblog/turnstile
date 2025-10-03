<?php

namespace AxCF\Turnstile;

use XF\App;
use function in_array;
use function is_array;
use function is_string;

class Config
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function getSiteKey(): string
    {
        return $this->stringValue('axcfTurnstileSiteKey', 'siteKey');
    }

    public function getSecretKey(): string
    {
        return $this->stringValue('axcfTurnstileSecretKey', 'secretKey');
    }

    public function getTheme(): string
    {
        $theme = $this->stringValue('axcfTurnstileTheme', 'theme', 'auto');

        return in_array($theme, ['auto', 'light', 'dark'], true) ? $theme : 'auto';
    }

    public function getSize(): string
    {
        $size = $this->stringValue('axcfTurnstileSize', 'size', 'normal');

        return in_array($size, ['normal', 'compact'], true) ? $size : 'normal';
    }

    public function toArray(): array
    {
        return [
            'siteKey' => $this->getSiteKey(),
            'secretKey' => $this->getSecretKey(),
            'theme' => $this->getTheme(),
            'size' => $this->getSize()
        ];
    }

    private function stringValue(string $optionKey, string $configKey, string $default = ''): string
    {
        $optionValue = $this->getOptionValue($optionKey);
        if ($optionValue !== '')
        {
            return $optionValue;
        }

        $configValue = $this->getConfigValue($configKey);
        if ($configValue !== '')
        {
            return $configValue;
        }

        return $default;
    }

    private function getOptionValue(string $key): string
    {
        $options = $this->app->options();

        if (isset($options->$key))
        {
            $value = $options->$key;

            if (is_string($value))
            {
                return trim($value);
            }
        }

        return '';
    }

    private function getConfigValue(string $key): string
    {
        $config = $this->app->config()['axcfTurnstile'] ?? [];
        if (!is_array($config))
        {
            return '';
        }

        $value = $config[$key] ?? '';

        return is_string($value) ? trim($value) : '';
    }
}
