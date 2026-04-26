<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class DomainCheck implements VerificationStepInterface
{
    public function name(): string
    {
        return 'domain_check';
    }

    public function process(VerificationContext $context): void
    {
        if (empty($context->domain) || strpos($context->domain, '.') === false) {
            $context->setSignal('domain_check', 'invalid_domain');
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::UNDELIVERABLE->value, 'invalid_domain');
            return;
        }

        $context->setSignal('domain_check', 'ok');
    }
}
