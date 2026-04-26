<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class SyntaxCheck implements VerificationStepInterface
{
    public function name(): string
    {
        return 'syntax_check';
    }

    public function process(VerificationContext $context): void
    {
        if (!filter_var($context->email, FILTER_VALIDATE_EMAIL)) {
            $context->setSignal('stage', 'syntax_check');
            $context->setSignal('reason', 'invalid_syntax');
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::UNDELIVERABLE->value, 'invalid_syntax');
        }
    }
}
