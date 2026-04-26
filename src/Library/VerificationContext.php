<?php

namespace Acelle\Server\Library;

class VerificationContext
{
    public string $email;
    public ?string $domain = null;
    public ?string $localPart = null;

    public ?string $status = null;
    public bool $stop = false;
    public array $signals = [];
    public array $calledHandlers = [];

    public function __construct(string $email)
    {
        $this->email = trim($email);
        $this->extractEmailParts();
    }

    public function setSignal(string $key, $value): void
    {
        $this->signals[$key] = $value;
    }

    public function getSignal(string $key, $default = null)
    {
        return $this->signals[$key] ?? $default;
    }

    public function markHandlerCalled(string $handlerName): void
    {
        $this->calledHandlers[] = $handlerName;
        $this->setSignal('called_handlers', $this->calledHandlers);
    }

    public function stopWithStatus(string $status, ?string $reason = null): void
    {
        $this->status = $status;
        $this->stop = true;
        if (!is_null($reason)) {
            $this->setSignal('stop_reason', $reason);
        }
    }

    private function extractEmailParts(): void
    {
        if (strpos($this->email, '@') === false) {
            return;
        }

        [$localPart, $domain] = explode('@', $this->email, 2);
        $this->localPart = strtolower(trim($localPart));
        $this->domain = strtolower(trim($domain));
    }
}
