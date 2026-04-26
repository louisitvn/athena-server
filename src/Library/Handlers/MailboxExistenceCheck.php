<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class MailboxExistenceCheck implements VerificationStepInterface
{
    public function name(): string
    {
        return 'mailbox_existence_check';
    }

    public function process(VerificationContext $context): void
    {
        $smtpCode = $context->getSignal('smtp_code');

        if (is_null($smtpCode)) {
            $context->setSignal('mailbox_exists', null);
            return;
        }

        if ($smtpCode >= 200 && $smtpCode < 300) {
            $context->setSignal('mailbox_exists', true);
            if (is_null($context->status)) {
                // NOTE: For now, stop immediately after deciding status.
                // This can be relaxed later to let next steps run and use status as reference.
                $context->stopWithStatus(VerificationStatus::DELIVERABLE->value, 'mailbox_exists');
            }
            return;
        }

        if ($smtpCode >= 500 && $smtpCode < 600) {
            $context->setSignal('mailbox_exists', false);
            return;
        }

        $context->setSignal('mailbox_exists', null);
    }
}
