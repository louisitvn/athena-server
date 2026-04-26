<?php

namespace Acelle\Server\Library\Handlers;

use Acelle\Server\Library\VerificationContext;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Library\VerificationStepInterface;

class TypoSuggestionCheck implements VerificationStepInterface
{
    private array $typoMap;

    public function __construct(array $typoMap = [
        'gmial.com' => 'gmail.com',
        'gamil.com' => 'gmail.com',
        'hotnail.com' => 'hotmail.com',
        'yaho.com' => 'yahoo.com',
    ]) {
        $this->typoMap = $typoMap;
    }

    public function name(): string
    {
        return 'typo_suggestion_check';
    }

    public function process(VerificationContext $context): void
    {
        if (empty($context->domain)) {
            return;
        }

        $lowerDomain = strtolower($context->domain);

        if (!array_key_exists($lowerDomain, $this->typoMap)) {
            return;
        }

        $suggestion = sprintf('%s@%s', $context->localPart, $this->typoMap[$lowerDomain]);
        $context->setSignal('typo_suggestion', $suggestion);

        if (is_null($context->status)) {
            // NOTE: For now, stop immediately after deciding status.
            // This can be relaxed later to let next steps run and use status as reference.
            $context->stopWithStatus(VerificationStatus::RISKY->value, 'typo_suggestion');
        }
    }
}
