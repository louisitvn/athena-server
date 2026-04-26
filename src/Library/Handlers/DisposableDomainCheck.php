<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class DisposableDomainCheck implements VerificationStepInterface
{
    private array $disposableDomains;

    public function __construct(array $disposableDomains = [])
    {
        $this->disposableDomains = array_map('strtolower', $disposableDomains);
    }

    public function name(): string
    {
        return 'disposable_domain_check';
    }

    public function process(VerificationContext $context): void
    {
        if (empty($context->domain)) {
            return;
        }

        if (in_array($context->domain, $this->disposableDomains, true)) {
            $context->setSignal('disposable_domain', true);

            if (is_null($context->status)) {
                // NOTE: For now, stop immediately after deciding status.
                // This can be relaxed later to let next steps run and use status as reference.
                $context->stopWithStatus(VerificationStatus::RISKY->value, 'disposable_domain');
            }
            return;
        }

        $context->setSignal('disposable_domain', false);
    }
}
