<?php

namespace Acelle\Server\Console;

use Acelle\Server\Model\VerificationCampaign;
use Acelle\Server\Library\VerificationStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CampaignsReport extends Command
{
    protected $signature = 'campaigns:report';

    protected $description = 'Show all verification campaigns (▶ = running) and drill into a specific one';

    public function handle(): int
    {
        $campaigns = VerificationCampaign::orderByRaw("FIELD(status, 'running') DESC")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($campaigns->isEmpty()) {
            $this->warn('No campaigns found.');
            return 0;
        }

        $this->renderList($campaigns);

        $id = $this->ask('Enter campaign ID to view full report (or press Enter to exit)');

        if (empty($id)) {
            return 0;
        }

        $campaign = $campaigns->firstWhere('id', (int) $id);

        if (!$campaign) {
            $this->error("Campaign #{$id} not found in the list above.");
            return 1;
        }

        $this->renderDetail($campaign);

        return 0;
    }

    private function renderList($campaigns): void
    {
        $rows = $campaigns->map(function (VerificationCampaign $c) {
            $total    = $c->emailAddresses()->count();
            $verified = $c->emailAddresses()->verified()->count();
            $pending  = $total - $verified;
            $percent  = $total > 0 ? round($verified / $total * 100, 1) : 0;

            $lastAt = $c->emailAddresses()
                ->whereNotNull('last_verification_at')
                ->max('last_verification_at');

            $name = ($c->isRunning() ? '▶ ' : '  ') . ($c->file_name ?? '(no name)');

            return [
                $c->id,
                $name,
                Carbon::parse($c->created_at)->diffForHumans(),
                number_format($total),
                number_format($verified) . " ({$percent}%)",
                number_format($pending),
                $lastAt ? Carbon::parse($lastAt)->diffForHumans() : '—',
            ];
        });

        $this->table(
            ['ID', 'Name', 'Started', 'Total', 'Verified', 'Pending', 'Last verified at'],
            $rows
        );
    }

    private function renderDetail(VerificationCampaign $campaign): void
    {
        $this->newLine();
        $this->line("<fg=cyan;options=bold>═══════════════════════════════════════════════════════════</>");
        $this->line("<fg=cyan;options=bold>  Campaign #{$campaign->id}: " . ($campaign->file_name ?? '(no name)') . "</>");
        $this->line("<fg=cyan;options=bold>═══════════════════════════════════════════════════════════</>");
        $this->newLine();

        // ── Counts ────────────────────────────────────────────────
        $total    = $campaign->emailAddresses()->count();
        $verified = $campaign->emailAddresses()->verified()->count();
        $pending  = $total - $verified;
        $percent  = $total > 0 ? round($verified / $total * 100, 1) : 0;

        $lastVerifiedAt = $campaign->emailAddresses()
            ->whereNotNull('last_verification_at')
            ->max('last_verification_at');

        // ── Overview ─────────────────────────────────────────────
        $this->line('<fg=yellow;options=bold>● Overview</>');
        $this->table(['Field', 'Value'], [
            ['Status',               strtoupper($campaign->status)
                                     . ($campaign->isError() ? '  ← ' . $campaign->error : '')],
            ['Started',              Carbon::parse($campaign->created_at)->diffForHumans()
                                     . '  (' . $campaign->created_at->format('Y-m-d H:i:s') . ')'],
            ['Last activity',        $lastVerifiedAt
                                     ? Carbon::parse($lastVerifiedAt)->diffForHumans()
                                       . '  (' . Carbon::parse($lastVerifiedAt)->format('Y-m-d H:i:s') . ')'
                                     : '—'],
        ]);

        // ── Overall progress ─────────────────────────────────────
        $this->line('<fg=yellow;options=bold>● Overall Progress</>');

        $barFilled = $total > 0 ? (int) round($percent / 2) : 0;
        $bar       = '<fg=green>' . str_repeat('█', $barFilled) . '</>'
                   . str_repeat('░', 50 - $barFilled);

        $this->line("  {$bar}  {$percent}%");
        $this->newLine();

        $this->table(['Metric', 'Count'], [
            ['Total emails',  number_format($total)],
            ['Verified',      number_format($verified) . "  ({$percent}%)"],
            ['Pending',       number_format($pending)],
        ]);

        // ── Status breakdown ─────────────────────────────────────
        $this->line('<fg=yellow;options=bold>● Status Breakdown</>');

        $statusCounts = $campaign->emailAddresses()
            ->selectRaw('verification_status, COUNT(*) as cnt')
            ->groupBy('verification_status')
            ->orderByRaw("FIELD(verification_status, 'deliverable','risky','unknown','undeliverable','new')")
            ->pluck('cnt', 'verification_status');

        $statusColors = [
            'deliverable'   => 'green',
            'undeliverable' => 'red',
            'risky'         => 'yellow',
            'unknown'       => 'white',
            'new'           => 'gray',
        ];

        $breakdownRows = $statusCounts->map(function ($cnt, $status) use ($total, $statusColors) {
            $pct      = $total > 0 ? round($cnt / $total * 100, 1) : 0;
            $filled   = (int) round($pct / 2);
            $color    = $statusColors[$status] ?? 'white';
            $bar      = "<fg={$color}>" . str_repeat('█', $filled) . '</>' . str_repeat('░', 50 - $filled);
            return [ucfirst($status), number_format($cnt), "{$pct}%", $bar];
        })->values()->toArray();

        $this->table(['Status', 'Count', '%', 'Distribution'], $breakdownRows);

        // ── Server breakdown ─────────────────────────────────────
        $this->line('<fg=yellow;options=bold>● Verification Servers</>');

        $serverRows = $campaign->emailAddresses()
            ->whereNotNull('last_verification_by')
            ->selectRaw('last_verification_by, COUNT(*) as cnt')
            ->groupBy('last_verification_by')
            ->orderByDesc('cnt')
            ->get()
            ->map(function ($row) use ($verified, $total) {
                $shareOfVerified = $verified > 0 ? round($row->cnt / $verified * 100, 1) : 0;
                $shareOfTotal    = $total    > 0 ? round($row->cnt / $total    * 100, 1) : 0;
                return [
                    $row->last_verification_by,
                    number_format($row->cnt),
                    "{$shareOfVerified}%  of verified",
                    "{$shareOfTotal}%  of total",
                ];
            })->toArray();

        if (empty($serverRows)) {
            $this->warn('  No emails verified yet.');
        } else {
            $this->table(['Server', 'Emails handled', 'Share (verified)', 'Share (total)'], $serverRows);
        }

        // ── Queue / Batch ─────────────────────────────────────────
        $this->line('<fg=yellow;options=bold>● Queue / Batch Jobs</>');

        $monitor = $campaign->jobMonitors()
            ->byJobType(VerificationCampaign::JOB_TYPE_VERIFY_LIST)
            ->latest()
            ->first();

        if (!$monitor) {
            $this->warn('  No job monitor found.');
        } else {
            $batch = $monitor->getBatch();

            if (!$batch) {
                $this->warn('  No batch found (job may have run synchronously).');
            } else {
                $batchBarFilled = (int) round($batch->progress() / 2);
                $batchBar       = '<fg=blue>' . str_repeat('█', $batchBarFilled) . '</>'
                                . str_repeat('░', 50 - $batchBarFilled);

                $this->line("  {$batchBar}  {$batch->progress()}%");
                $this->newLine();

                $rows = [
                    ['Batch ID',        $batch->id],
                    ['Total jobs',      number_format($batch->totalJobs)],
                    ['Processed',       number_format($batch->processedJobs())],
                    ['Pending',         number_format($batch->pendingJobs)],
                    ['Failed',          $batch->failedJobs > 0
                                        ? "<fg=red>{$batch->failedJobs}</>"
                                        : '0'],
                    ['Finished',        $batch->finished() ? '<fg=green>Yes</>' : 'No'],
                    ['Cancelled',       $batch->cancelled() ? '<fg=yellow>Yes</>' : 'No'],
                ];

                if ($batch->createdAt) {
                    $rows[] = ['Batch created', Carbon::parse($batch->createdAt)->diffForHumans()
                                                . '  (' . Carbon::parse($batch->createdAt)->format('Y-m-d H:i:s') . ')'];
                }
                if ($batch->finishedAt) {
                    $rows[] = ['Batch finished', Carbon::parse($batch->finishedAt)->diffForHumans()
                                                 . '  (' . Carbon::parse($batch->finishedAt)->format('Y-m-d H:i:s') . ')'];
                }

                $this->table(['Field', 'Value'], $rows);
            }
        }

        $this->newLine();
    }
}
