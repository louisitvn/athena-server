<?php

namespace Acelle\Server\Library;

class VerificationPipeline
{
    /**
     * @var VerificationStepInterface[]
     */
    private array $steps;

    private string $defaultStatus;

    /**
     * @param VerificationStepInterface[] $steps
     */
    public function __construct(array $steps, string $defaultStatus = VerificationStatus::UNKNOWN->value)
    {
        $this->steps = $steps;
        $this->defaultStatus = $defaultStatus;
    }

    public function verify(string $email): VerificationContext
    {
        $context = new VerificationContext($email);

        foreach ($this->steps as $step) {
            $context->markHandlerCalled($step->name());
            $step->process($context);
            if ($context->stop) {
                break;
            }
        }

        if (is_null($context->status)) {
            $context->status = $this->defaultStatus;
        }

        return $context;
    }
}
