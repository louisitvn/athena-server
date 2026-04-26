<?php

namespace Acelle\Server\Library;

interface VerificationStepInterface
{
    public function name(): string;

    public function process(VerificationContext $context): void;
}
