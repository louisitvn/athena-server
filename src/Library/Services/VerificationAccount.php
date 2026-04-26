<?php

namespace Acelle\Server\Library\Services;

use App\Library\ApiKey;
use App\Library\CredentialsInterface;
use App\Library\CredentialsType;
use App\Library\DynamicRateTracker;
use App\Library\RateLimit;
use App\Library\RateTracker;
use App\Library\RateTrackerInterface;
use App\Library\UsernamePassword;
use App\Library\VerificationAccountInterface;
use App\Library\VerificationServiceFactory;
use InvalidArgumentException;

class VerificationAccount implements VerificationAccountInterface
{
    protected $credentials;
    protected $name;
    protected $type;
    protected $service;

    public function __construct(array $credentials = [], ?string $name = null, ?string $type = null)
    {
        $this->credentials = $credentials;
        $this->name = $name ?: 'Verification Account';
        $this->type = $type ?: ($credentials['type'] ?? null);
    }

    public function getType(): string
    {
        if (empty($this->type)) {
            throw new InvalidArgumentException('Missing required verification service type');
        }

        return $this->type;
    }

    public function getCredentials(): CredentialsInterface
    {
        $rawType = $this->credentials['auth'] ?? $this->getServiceAuthType();

        if (empty($rawType)) {
            if (!empty($this->credentials['username']) && (!empty($this->credentials['api_token']) || !empty($this->credentials['password']))) {
                $rawType = CredentialsType::USERNAME_PASSWORD->value;
            } elseif (!empty($this->credentials['api_key'])) {
                $rawType = CredentialsType::API_KEY->value;
            }
        }

        $credentialsType = CredentialsType::tryFrom((string) $rawType);

        if (is_null($credentialsType)) {
            throw new InvalidArgumentException(sprintf('Unsupported credentials type for service `%s`: %s', $this->getType(), (string) $rawType));
        }

        if ($credentialsType === CredentialsType::API_KEY) {
            $apiKey = $this->credentials['api_key'] ?? null;

            if (empty($apiKey)) {
                throw new InvalidArgumentException('Missing required credential: api_key');
            }

            return new ApiKey($apiKey);
        }

        $username = $this->credentials['username'] ?? null;
        $password = $this->credentials['api_token'] ?? ($this->credentials['password'] ?? null);

        if (empty($username) || empty($password)) {
            throw new InvalidArgumentException('Missing required credentials: username and api_token/password');
        }

        return new UsernamePassword($username, $password);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRateTracker(): RateTrackerInterface
    {
        $limitPerMinute = (int) ($this->credentials['rate_limit_per_minute'] ?? 15);
        if ($limitPerMinute < 1) {
            $limitPerMinute = 1;
        }

        $limits = [
            new RateLimit($limitPerMinute, 1, 'minute', "Verification service rate limit {$limitPerMinute} per minute"),
        ];

        $resourceKey = 'verification-service-'.md5($this->getType().':'.($this->credentials['api_key'] ?? 'default'));

        if (config('custom.distributed_mode')) {
            return new DynamicRateTracker($resourceKey, $limits);
        }

        $file = storage_path("app/quota/{$resourceKey}");
        return new RateTracker($file, $limits);
    }

    public function verify(string $email): array
    {
        return $this->getServiceClient()->verify($email);
    }

    public function getServiceClient()
    {
        if (is_null($this->service)) {
            $this->service = VerificationServiceFactory::make($this->getType(), $this->getCredentials());
        }

        return $this->service;
    }

    protected function getServiceAuthType(): ?string
    {
        $services = config('verification.services', []);

        foreach ($services as $service) {
            if (($service['id'] ?? null) === $this->getType()) {
                return $service['auth'] ?? null;
            }
        }

        return null;
    }
}
