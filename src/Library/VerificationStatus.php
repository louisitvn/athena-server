<?php

namespace Acelle\Server\Library;

enum VerificationStatus: string
{
    case NEW = 'new';
    case DELIVERABLE = 'deliverable';
    case UNDELIVERABLE = 'undeliverable';
    case UNKNOWN = 'unknown';
    case RISKY = 'risky';
}
