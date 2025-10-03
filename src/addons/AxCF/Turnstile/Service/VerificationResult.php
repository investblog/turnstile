<?php

namespace AxCF\Turnstile\Service;

final class VerificationResult
{
    private bool $successful;

    /** @var string[] */
    private array $errors;

    private function __construct(bool $successful, array $errors = [])
    {
        $this->successful = $successful;
        $this->errors = $errors;
    }

    public static function success(): self
    {
        return new self(true);
    }

    public static function failure(array $errors = []): self
    {
        return new self(false, $errors);
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
