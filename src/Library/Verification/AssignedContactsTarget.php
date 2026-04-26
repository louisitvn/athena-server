<?php

namespace Acelle\Server\Library\Verification;

use App\Library\Contracts\BulkVerificationTargetInterface;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Model\EmailAddress;
use Acelle\Server\Model\VerificationCampaign;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

/**
 * Adapter implementation of BulkVerificationTargetInterface for partial campaign verification.
 *
 * While MailList and VerificationCampaign can be used as full verification targets,
 * this adapter wraps a VerificationCampaign with a scoped set of contact IDs so the
 * verifier can process only an assigned subset of contacts instead of the entire campaign.
 */
class AssignedContactsTarget implements BulkVerificationTargetInterface
{
    protected $campaign;

    /**
     * IDs of contacts to verify, represented by EmailAddress record IDs
     * returned from the campaign's emailAddresses() relation.
     *
     * @var int[]
     */
    protected $contactIds;

    public $customer;

    /**
     * @param VerificationCampaign $campaign Parent campaign used as data source/context.
     * @param int[] $contactIds EmailAddress IDs (contact IDs) inside this campaign to verify.
     */
    public function __construct(VerificationCampaign $campaign, array $contactIds)
    {
        $this->campaign = $campaign;
        $this->contactIds = array_values(array_unique(array_map('intval', $contactIds)));
        $this->customer = $campaign->customer;
    }

    public function logger(): LoggerInterface
    {
        return $this->campaign->logger();
    }

    public function getUnverifiedQuery(): Builder
    {
        return $this->scopedQuery()->new()->getQuery();
    }

    public function getVerifiedSubscribersPercentage(bool $cache = false): float
    {
        $total = $this->getSubscribersCount();

        if ($total === 0) {
            return 0.0;
        }

        return $this->getVerifiedSubscribersCount() / $total;
    }

    public function getVerifiedSubscribersCount(): int
    {
        return (int) $this->scopedQuery()->verified()->count();
    }

    public function getSubscribersCount(): int
    {
        return (int) $this->scopedQuery()->count();
    }

    public function getId(): int
    {
        return (int) $this->campaign->id;
    }

    public function updateVerificationResults(array $results, string $verificationBy): void
    {
        if (empty($results)) {
            $this->logger()->warning(sprintf('- [campaign:%s] Empty verification results', $this->getId()));
            return;
        }

        $resultsByEmail = [];
        foreach ($results as $row) {
            $email = $row['email'] ?? null;

            if (is_null($email)) {
                continue;
            }

            $resultsByEmail[$email] = [
                'status' => $row['status'] ?? VerificationStatus::UNKNOWN->value,
                'raw' => $row['raw'] ?? null,
            ];
        }

        if (empty($resultsByEmail)) {
            return;
        }

        $contacts = $this->scopedQuery()
            ->whereIn('email', array_keys($resultsByEmail))
            ->get();

        DB::transaction(function () use ($contacts, $resultsByEmail, $verificationBy) {
            $verifiedAt = now();

            foreach ($contacts as $contact) {
                $mapped = $resultsByEmail[$contact->email];
                $contact->verification_status = $mapped['status'];
                $contact->last_verification_at = $verifiedAt;
                $contact->last_verification_by = $verificationBy;
                $contact->last_verification_result = $mapped['raw'];
                $contact->save();
            }
        });
    }

    protected function scopedQuery()
    {
        $query = $this->campaign->emailAddresses();

        if (empty($this->contactIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id', $this->contactIds);
    }
}
