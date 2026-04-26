<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class SuppressionListCheck implements VerificationStepInterface
{
    private array $suppressedEmails;
    private array $suppressedDomains;

    public function __construct(array $suppressedEmails = [], array $suppressedDomains = [])
    {
        $this->suppressedEmails = array_map('strtolower', $suppressedEmails);
        $this->suppressedDomains = array_map('strtolower', $suppressedDomains);
    }

    public function name(): string
    {
        return 'suppression_list_check';
    }

    public function process(VerificationContext $context): void
    {
        $email = strtolower($context->email);
        $domain = strtolower((string) $context->domain);

        $isSuppressed = in_array($email, $this->suppressedEmails, true)
            || (!empty($domain) && in_array($domain, $this->suppressedDomains, true));

        $context->setSignal('suppressed', $isSuppressed);

        if ($isSuppressed) {
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::UNDELIVERABLE->value, 'suppressed');
        }
    }
}
