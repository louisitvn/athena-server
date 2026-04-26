<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class CatchAllCheck implements VerificationStepInterface
{
    private $detector;

    public function __construct(?callable $detector = null)
    {
        $this->detector = $detector;
    }

    public function name(): string
    {
        return 'catch_all_check';
    }

    public function process(VerificationContext $context): void
    {
        if (is_null($this->detector)) {
            $context->setSignal('catch_all', null);
            return;
        }

        $isCatchAll = (bool) call_user_func($this->detector, $context->email, $context->domain);
        $context->setSignal('catch_all', $isCatchAll);

        if ($isCatchAll && is_null($context->status)) {
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::RISKY->value, 'catch_all_domain');
        }
    }
}
