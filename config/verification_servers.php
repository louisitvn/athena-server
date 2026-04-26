<?php

return [
    // Weighted verification servers used by BulkVerifyOrchestrator.
    // Supports provider engines and Athena engine in the same pool.
    // Example split: AthenaEngine 40%, Server1 30%, Server2 30%.
    'servers' => [
        [
            'name' => 'athena-engine',
            'engine' => 'athena',
            'weight' => 40,
        ],
        [
            'name' => 'zerobounce-primary',
            'weight' => 30,
            'class' => 'App\\Library\\Services\\VerificationAccount',
            'type' => 'zerobounce.net',
            'credentials' => [
                'api_key' => '2f43dbce040a4f1c90dddc6757fdab2c',
                'rate_limit_per_minute' => 15,
            ],
        ],
        [
            'name' => 'fake-random-service',
            'weight' => 30,
            'class' => 'App\\Library\\Services\\FakeAccount',
            'credentials' => [
                'api_key' => 'fake-api-key',
            ],
        ],
    ],
];
