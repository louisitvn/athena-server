<?php

namespace Acelle\Server\Library;

use Acelle\Server\Library\Handlers\CatchAllCheck;
use Acelle\Server\Library\Handlers\DisposableDomainCheck;
use Acelle\Server\Library\Handlers\DomainCheck;
use Acelle\Server\Library\Handlers\GreylistCheck;
use Acelle\Server\Library\Handlers\MailboxExistenceCheck;
use Acelle\Server\Library\Handlers\MxLookupCheck;
use Acelle\Server\Library\Handlers\RiskScoreAggregator;
use Acelle\Server\Library\Handlers\RoleAccountCheck;
use Acelle\Server\Library\Handlers\SmtpHandshakeCheck;
use Acelle\Server\Library\Handlers\SuppressionListCheck;
use Acelle\Server\Library\Handlers\SyntaxCheck;
use Acelle\Server\Library\Handlers\TypoSuggestionCheck;
use App\Library\Contracts\BulkVerificationTargetInterface;
use Acelle\Server\Jobs\AthenaVerifyContact;
use Illuminate\Bus\Batch;

class AthenaEngine
{

    /**
     * @var VerificationStepInterface[]
     */
    protected array $handlers;

    public function getName(): string
    {
        return 'AthenaEngine';
    }

    public function getType(): string
    {
        return 'athena';
    }

    public function __construct(?array $handlers = null)
    {
        $this->handlers = $handlers ?? $this->defaultHandlers();
    }

    public function run(\App\Library\Contracts\BulkVerificationTargetInterface $target, ?Batch $batch = null): void
    {
        $target->getUnverifiedQuery()->chunkById(1000, function ($contacts) use ($target, $batch) {
            $jobs = [];

            foreach ($contacts as $contact) {
                $jobs[] = (new AthenaVerifyContact($target, $contact->email, $this->handlers))
                    ->onQueue(ACM_QUEUE_TYPE_BATCH);
            }

            if (empty($jobs)) {
                return;
            }

            if (!is_null($batch)) {
                $target->logger()->info(sprintf(
                    '[AthenaEngine] Adding %s AthenaVerifyContact jobs to batch %s',
                    count($jobs),
                    $batch->id
                ));
                $batch->add($jobs);
                return;
            }

            $target->logger()->info(sprintf(
                '[AthenaEngine] Batch unavailable, dispatching %s AthenaVerifyContact jobs directly to queue `%s`',
                count($jobs),
                ACM_QUEUE_TYPE_BATCH
            ));

            foreach ($jobs as $job) {
                dispatch($job);
            }
        });
        return;
    }

    public function verifySingle(string $email): array
    {
        $pipeline = new VerificationPipeline($this->handlers);
        $context = $pipeline->verify($email);

        $raw = [
            'engine' => 'athenaengine',
            'status' => $context->status,
            'signals' => $context->signals,
            'mxs' => [],
        ];

        return [$context->status, json_encode($raw)];
    }

    protected function defaultHandlers(): array
    {
        return [
            new SyntaxCheck(),
            new DomainCheck(),
            new MxLookupCheck(),
            new SuppressionListCheck(),
            new DisposableDomainCheck(),
            new RoleAccountCheck(),
            new CatchAllCheck(),
            new SmtpHandshakeCheck(),
            new GreylistCheck(),
            new MailboxExistenceCheck(),
            new TypoSuggestionCheck(),
            new RiskScoreAggregator(),
        ];
    }
}
