<?php

namespace Acelle\Server\Jobs;

use Acelle\Server\Library\Verification\AssignedContactsTarget;
use Acelle\Server\Library\AthenaEngine;
use App\Jobs\BulkVerify;
use App\Library\Traits\Trackable;
use App\Library\VerificationAccountInterface;
use App\Model\Subscription;
use App\Services\Plans\Credits\CreditsService;
use Acelle\Server\Model\VerificationCampaign;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Exception;
use Throwable;

class BulkVerifyOrchestrator implements ShouldQueue
{
    use Trackable;
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const CHUNK_SIZE = 1000;

    public $tries = 1;

    protected $campaign;
    protected $servers;
    protected $subscription;

    public function __construct(
        VerificationCampaign $campaign,
        Subscription $subscription
    ) {
        $this->campaign = $campaign;
        $this->subscription = $subscription;
    }

    public function handle()
    {
        $this->campaign->customer->setUserDbConnection();

        $logger = $this->campaign->logger();
        $logger->info(sprintf('[BulkVerifyOrchestrator] Start orchestrating bulk verification for campaign #%s', $this->campaign->id));

        try {
            $serverConfig = config('verification_servers.servers', []);
            $this->servers = $this->normalizeServers($serverConfig);

            if (empty($this->servers)) {
                $msg = '[BulkVerifyOrchestrator] No verification servers provided, aborting';
                $logger->warning($msg);
                throw new Exception($msg);
            }

            $logger->info(sprintf('[BulkVerifyOrchestrator] Servers and weights: %s', $this->formatServersForLog($this->servers)));

            $totalUnverified = (int) $this->campaign->emailAddresses()->new()->count();
            $totalPages = $totalUnverified === 0 ? 0 : (int) ceil($totalUnverified / self::CHUNK_SIZE);

            $logger->info(sprintf(
                '[BulkVerifyOrchestrator] Unverified=%s, chunk_size=%s, pages=%s',
                $totalUnverified,
                self::CHUNK_SIZE,
                $totalPages
            ));

            $chunkIndex = 0;

            $this->campaign->emailAddresses()
                ->new()
                ->select('id')
                ->orderBy('id')
                ->chunkById(self::CHUNK_SIZE, function ($rows) use (&$chunkIndex, $logger, $totalPages) {
                    $chunkIndex++;
                    $chunkIds = $rows->pluck('id')->map(fn ($id) => (int) $id)->all();

                    $logger->info(sprintf(
                        '[BulkVerifyOrchestrator] Processing page %s/%s (chunk #%s), size=%s',
                        $chunkIndex,
                        $totalPages,
                        $chunkIndex,
                        count($chunkIds)
                    ));

                    // Sample value:
                    // [
                    //   ['server' => AthenaEngine instance, 'ids' => [101, 205]],
                    //   ['server' => VerificationAccountInterface instance, 'ids' => [77, 88]],
                    // ]
                    $assignedGroups = $this->splitForServers($chunkIds, $this->servers);

                    $jobs = [];

                    foreach ($assignedGroups as $group) {
                        $ids = $group['ids'];
                        if (empty($ids)) {
                            continue;
                        }

                        $server = $group['server'];

                        $target = new AssignedContactsTarget($this->campaign, $ids);

                        if ($server instanceof AthenaEngine) {
                            $logger->info(sprintf(
                                '[BulkVerifyOrchestrator] Start AthenaEngine for chunk #%s with %s contacts',
                                $chunkIndex,
                                count($ids)
                            ));

                            $server->run($target, $this->batch());
                            continue;
                        }

                        // Provider service will be started for this chunk
                        $jobs[] = $this->makeBulkVerifyJob($target, $server);
                    }

                    if (empty($jobs)) {
                        $logger->info(sprintf('[BulkVerifyOrchestrator] Chunk #%s has no provider jobs to dispatch', $chunkIndex));
                        return;
                    }

                    if (!is_null($this->batch())) {
                        $logger->info(sprintf('[BulkVerifyOrchestrator] Adding %s jobs to batch %s', count($jobs), $this->batch()->id));
                        $this->batch()->add($jobs);
                    } else {
                        $logger->info(sprintf('[BulkVerifyOrchestrator] Dispatching %s jobs directly to queue `%s`', count($jobs), ACM_QUEUE_TYPE_BATCH));
                        foreach ($jobs as $job) {
                            dispatch($job->onQueue(ACM_QUEUE_TYPE_BATCH));
                        }
                    }
                });

            $logger->info(sprintf('[BulkVerifyOrchestrator] Done orchestrating for campaign #%s', $this->campaign->id));
        } catch (Throwable $e) {
            $logger->error(sprintf('[BulkVerifyOrchestrator] Exception: %s', $e->getMessage()));
            throw $e;
        }
    }

    /**** This makes test failed to execute (job not executed) 
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('bulk-verify-orchestrator:campaign:'.$this->campaign->id))
                ->dontRelease()
                ->expireAfter(3600),
        ];
    }
    */

    protected function makeBulkVerifyJob(AssignedContactsTarget $target, VerificationAccountInterface $server): BulkVerify
    {
        // Wait: not this type of credit
        //   $creditTracker = app(CreditsService::class)->verifyEmailTracker($this->subscription);
        // It should be something like
        //   $creditTracker = app(CreditsService::class)->selfVerifyCreditsTracker($this->subscription);
        $creditTracker = null; // temporary null, not credit enforcement for now

        $job = new BulkVerify($target, $server, $creditTracker);

        if (!is_null($this->monitor)) {
            $job->setMonitor($this->monitor);
        }

        return $job;
    }

    protected function normalizeServers(array $servers): array
    {
        $normalized = [];

        foreach ($servers as $index => $entry) {
            if ($entry instanceof VerificationAccountInterface) {
                $normalized[] = [
                    'weight' => 1,
                    'server' => $entry,
                ];
                continue;
            }

            if (!is_array($entry)) {
                continue;
            }

            $configuredEngine = array_key_exists('engine', $entry)
                ? strtolower((string) $entry['engine'])
                : null;

            if (!is_null($configuredEngine) && $configuredEngine !== 'athena') {
                throw new Exception(sprintf('Invalid verification engine `%s`. Only `athena` is supported when `engine` is set', $configuredEngine));
            }

            $weight = (int) ($entry['weight'] ?? 1);
            if ($weight < 0) {
                $weight = 0;
            }

            if ($configuredEngine === 'athena') {
                if (!array_key_exists('name', $entry) || !is_string($entry['name']) || trim($entry['name']) === '') {
                    throw new Exception('Athena engine entry must define a non-empty `name`');
                }

                if (array_key_exists('class', $entry) || array_key_exists('server', $entry)) {
                    throw new Exception('Athena engine entry must not define `class` or `server`');
                }

                $athenaHandlers = $entry['handlers'] ?? null;
                $engineInstance = new AthenaEngine($athenaHandlers);

                $normalized[] = [
                    'weight' => $weight,
                    'server' => $engineInstance,
                ];

                continue;
            }

            $server = $entry['server'] ?? null;

            if (is_null($server) && (!array_key_exists('class', $entry) || empty($entry['class']))) {
                throw new Exception('Provider engine entry must define either `server` instance or `class`');
            }

            if (!$server instanceof VerificationAccountInterface) {
                $server = $this->buildServerFromConfig($entry);
            }

            if (!$server instanceof VerificationAccountInterface) {
                $entryJson = @json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if ($entryJson === false) {
                    $entryJson = print_r($entry, true);
                }
                throw new Exception(sprintf('Server entry at index %s is not an instance of VerificationAccountInterface. Entry: %s', $index, $entryJson));
            }

            $normalized[] = [
                'weight' => $weight,
                'server' => $server,
            ];
        }

        if (!empty($normalized)) {
            $totalWeight = array_sum(array_map(function ($entry) {
                return (int) $entry['weight'];
            }, $normalized));

            if ($totalWeight !== 100) {
                throw new Exception(sprintf('Invalid verification_servers.servers config: total weight must be 100, got %s', $totalWeight));
            }
        }

        return $normalized;
    }

    protected function buildServerFromConfig(array $entry): ?VerificationAccountInterface
    {
        $className = $entry['class'] ?? null;

        if (empty($className) || !is_string($className)) {
            throw new Exception('Missing required `class` in verification_servers.servers entry');
        }

        $className = str_replace('/', '\\', $className);

        if (!class_exists($className)) {
            throw new Exception(sprintf('Configured verification server class not found: %s', $className));
        }

        if (!is_subclass_of($className, VerificationAccountInterface::class)) {
            throw new Exception(sprintf('Configured class must implement VerificationAccountInterface: %s', $className));
        }

        $credentials = $entry['credentials'] ?? [];
        if (!is_array($credentials)) {
            $credentials = [];
        }

        $name = $entry['name'] ?? null;
        $type = $entry['type'] ?? null;

        try {
            $instance = new $className($credentials, $name, $type);
        } catch (\ArgumentCountError $ex) {
            try {
                $instance = new $className($credentials, $name);
            } catch (\ArgumentCountError $ex) {
                $instance = new $className($credentials);
            }
        }

        if (method_exists($instance, 'getServiceClient')) {
            $instance->getServiceClient();
        }

        return $instance;
    }

    protected function splitForServers(array $chunkIds, array $serverEntries): array
    {
        shuffle($chunkIds);

        if (empty($serverEntries)) {
            return [];
        }

        $weights = array_map(function ($entry) {
            return (int) ($entry['weight'] ?? 0);
        }, $serverEntries);

        $sumWeight = array_sum($weights);

        $totalIds = count($chunkIds);

        $quotas = [];
        $assignedBase = 0;

        foreach ($serverEntries as $index => $entry) {
            $exact = $totalIds * $weights[$index] / $sumWeight;
            $base = (int) floor($exact);

            $quotas[$index] = [
                'base' => $base,
                'remainder' => $exact - $base,
            ];

            $assignedBase += $base;
        }

        $remainingSlots = $totalIds - $assignedBase;

        uasort($quotas, function ($left, $right) {
            if ($left['remainder'] === $right['remainder']) {
                return 0;
            }

            return $left['remainder'] > $right['remainder'] ? -1 : 1;
        });

        foreach ($quotas as $index => $quota) {
            if ($remainingSlots <= 0) {
                break;
            }

            $quotas[$index]['base'] += 1;
            $remainingSlots -= 1;
        }

        ksort($quotas);

        $cursor = 0;
        $groups = [];
        foreach ($serverEntries as $index => $entry) {
            $take = $quotas[$index]['base'];
            $ids = array_slice($chunkIds, $cursor, $take);
            $cursor += $take;

            $groups[] = [
                'server' => $entry['server'] ?? null,
                'ids' => $ids,
            ];
        }

        return $groups;
    }

    protected function formatServersForLog(array $servers): string
    {
        $parts = array_map(function ($entry) {
            $weight = (int) ($entry['weight'] ?? 0);
            $engine = $entry['engine'] ?? null;
            $server = $entry['server'] ?? null;

            $name = $server ? $server->getName() : 'provider';
            $type = $server ? $server->getType() : ($engine ?? 'n/a');

            return sprintf('%s(weight=%s,type=%s)', $name, $weight, $type);
        }, $servers);

        return implode(', ', $parts);
    }
}
