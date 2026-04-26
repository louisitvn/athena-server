<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class GreylistCheck implements VerificationStepInterface
{
    private array $greylistCodes;

    public function __construct(array $greylistCodes = [421, 450, 451, 452])
    {
        $this->greylistCodes = $greylistCodes;
    }

    public function name(): string
    {
        return 'greylist_check';
    }

    public function process(VerificationContext $context): void
    {
        $smtpCode = $context->getSignal('smtp_code');

        if (is_null($smtpCode)) {
            $context->setSignal('greylisted', false);
            return;
        }

        $greylisted = in_array((int) $smtpCode, $this->greylistCodes, true);
        $context->setSignal('greylisted', $greylisted);

        if ($greylisted && is_null($context->status)) {
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::UNKNOWN->value, 'greylisted');
        }
    }
}
