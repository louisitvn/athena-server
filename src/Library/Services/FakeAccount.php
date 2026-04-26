<?php

namespace Acelle\Server\Library\Services;

use App\Library\ApiKey;
use App\Library\CredentialsInterface;
use App\Library\DynamicRateTracker;
use App\Library\Everification\FakeService;
use App\Library\RateLimit;
use App\Library\RateTracker;
use App\Library\RateTrackerInterface;
use App\Library\VerificationAccountInterface;

class FakeAccount implements VerificationAccountInterface
{
    public const SERVICE_TYPE = 'fake.service';

    protected $credentials;
    protected $name;
    protected $service;

    public function __construct(array $credentials = [], ?string $name = null)
    {
        $this->credentials = $credentials;
        $this->name = $name ?: 'Fake Account';
    }

    public function getType(): string
    {
        return static::SERVICE_TYPE;
    }

    public function getCredentials(): CredentialsInterface
    {
        $apiKey = $this->credentials['api_key'] ?? 'fake-api-key';

        return new ApiKey($apiKey);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRateTracker(): RateTrackerInterface
    {
        $limits = [
            new RateLimit(1000000, 1, 'minute', 'Fake service high limit'),
        ];

        $resourceKey = 'verification-service-fake';

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

    public function getServiceClient(): FakeService
    {
        if (is_null($this->service)) {
            $this->service = new FakeService($this->credentials);
        }

        return $this->service;
    }
}
