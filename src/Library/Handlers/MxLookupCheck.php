<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class MxLookupCheck implements VerificationStepInterface
{
    private $resolver;

    public function __construct(?callable $resolver = null)
    {
        $this->resolver = $resolver;
    }

    public function name(): string
    {
        return 'mx_lookup_check';
    }

    public function process(VerificationContext $context): void
    {
        if (empty($context->domain)) {
            $context->setSignal('mx', false);
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::UNDELIVERABLE->value, 'missing_domain_for_mx');
            return;
        }

        if (!is_null($this->resolver)) {
            $hasMx = (bool) call_user_func($this->resolver, $context->domain);
        } else {
            $hasMx = checkdnsrr($context->domain, 'MX')
                || checkdnsrr($context->domain, 'A')
                || checkdnsrr($context->domain, 'AAAA');
        }

        $context->setSignal('mx', $hasMx);

        if (!$hasMx) {
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::UNDELIVERABLE->value, 'no_mx_record');
        }
    }
}
