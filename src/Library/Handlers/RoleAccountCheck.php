<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class RoleAccountCheck implements VerificationStepInterface
{
    private array $roleLocalParts;

    public function __construct(array $roleLocalParts = ['admin', 'info', 'support', 'sales', 'noreply', 'no-reply'])
    {
        $this->roleLocalParts = array_map('strtolower', $roleLocalParts);
    }

    public function name(): string
    {
        return 'role_account_check';
    }

    public function process(VerificationContext $context): void
    {
        $isRole = !empty($context->localPart) && in_array($context->localPart, $this->roleLocalParts, true);
        $context->setSignal('role_account', $isRole);

        if ($isRole && is_null($context->status)) {
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::RISKY->value, 'role_account');
        }
    }
}
