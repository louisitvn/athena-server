<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class RiskScoreAggregator implements VerificationStepInterface
{
    public function name(): string
    {
        return 'risk_score_aggregator';
    }

    public function process(VerificationContext $context): void
    {
        if (!is_null($context->status) && $context->status === VerificationStatus::UNDELIVERABLE->value) {
            return;
        }

        $riskScore = 0;

        if ($context->getSignal('disposable_domain') === true) {
            $riskScore += 30;
        }

        if ($context->getSignal('role_account') === true) {
            $riskScore += 20;
        }

        if ($context->getSignal('catch_all') === true) {
            $riskScore += 25;
        }

        if ($context->getSignal('greylisted') === true) {
            $riskScore += 20;
        }

        if (!is_null($context->getSignal('typo_suggestion'))) {
            $riskScore += 10;
        }

        $context->setSignal('risk_score', $riskScore);

        if (is_null($context->status)) {
            if ($riskScore >= 40) {
                // NOTE: For now, stop immediately after deciding status.
                // This can be relaxed later to let next steps run and use status as reference.
                $context->stopWithStatus(VerificationStatus::RISKY->value, 'risk_score_high');
            } elseif ($context->getSignal('mailbox_exists') === true || $context->getSignal('mx') === true) {
                // NOTE: For now, stop immediately after deciding status.
                // This can be relaxed later to let next steps run and use status as reference.
                $context->stopWithStatus(VerificationStatus::DELIVERABLE->value, 'risk_score_deliverable');
            } else {
                // NOTE: For now, stop immediately after deciding status.
                // This can be relaxed later to let next steps run and use status as reference.
                $context->stopWithStatus(VerificationStatus::UNKNOWN->value, 'risk_score_unknown');
            }
        }
    }
}
