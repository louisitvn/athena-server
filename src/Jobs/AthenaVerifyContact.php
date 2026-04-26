<?php

namespace Acelle\Server\Jobs;

use Acelle\Server\Library\VerificationPipeline;
use Acelle\Server\Library\VerificationStepInterface;
use App\Library\Contracts\BulkVerificationTargetInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AthenaVerifyContact implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    protected BulkVerificationTargetInterface $target;

    protected string $email;

    /**
     * @var VerificationStepInterface[]
     */
    protected array $handlers;

    public function __construct(BulkVerificationTargetInterface $target, string $email, array $handlers)
    {
        $this->target = $target;
        $this->email = $email;
        $this->handlers = $handlers;
    }

    public function handle(): void
    {
        $this->target->customer->setUserDbConnection();

        $pipeline = new VerificationPipeline($this->handlers);
        $context = $pipeline->verify($this->email);

        $verificationBy = 'AthenaEngine';

        $this->target->updateVerificationResults([
            [
                'email' => $this->email,
                'status' => $context->status,
                'raw' => json_encode([
                    'engine' => strtolower($verificationBy),
                    'status' => $context->status,
                    'signals' => $context->signals,
                ]),
            ],
        ], $verificationBy);
    }
}
