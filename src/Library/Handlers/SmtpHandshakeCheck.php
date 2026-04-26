<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class SmtpHandshakeCheck implements VerificationStepInterface
{
    private $probe;

    public function __construct(?callable $probe = null)
    {
        $this->probe = $probe;
    }

    public function name(): string
    {
        return 'smtp_handshake_check';
    }

    public function process(VerificationContext $context): void
    {
        if (is_null($this->probe)) {
            $context->setSignal('smtp_code', null);
            return;
        }

        $code = (int) call_user_func($this->probe, $context->email, $context->domain);
        $context->setSignal('smtp_code', $code);

        if ($code >= 500 && $code < 600) {
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::UNDELIVERABLE->value, 'smtp_permanent_failure');
            return;
        }

        if ($code >= 400 && $code < 500 && is_null($context->status)) {
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::UNKNOWN->value, 'smtp_transient_failure');
        }
    }
}
