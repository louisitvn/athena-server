<?php

namespace Acelle\Server\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use DB;
use Exception;
use League\Csv\Writer;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerInterface;

use App\Model\Customer;
use App\Library\Traits\HasUid;
use App\Library\Traits\TrackJobs;
use App\Library\Contracts\BulkVerificationTargetInterface;
use Acelle\Server\Library\VerificationStatus;
use Acelle\Server\Jobs\BulkVerifyOrchestrator;

use function App\Helpers\create_temp_db_table;

class VerificationCampaign extends Model implements BulkVerificationTargetInterface
{
    use HasFactory;
    use HasUid;

    use TrackJobs;

    public const TYPE_SINGLE = 'single';
    public const TYPE_UPLOAD = 'upload';
    public const TYPE_BULK = 'bulk';

    public const STATUS_NEW = 'new';
    public const STATUS_ERROR = 'error';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PAUSED = 'paused';

    public const JOB_TYPE_VERIFY_LIST = 'athena-bulk-verify';

    protected $logger;

    public function mapping()
    {
        switch ($this->type) {
            case self::TYPE_UPLOAD:
                return VerificationCampaign::find($this->id);
        }
    }
    

    public static function scopeSearch($query, $keyword)
    {
        // Keyword
        if (!empty(trim($keyword))) {
            $query = $query->where('file_name', 'like', '%'.trim($keyword).'%');
        }
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function isNew()
    {
        return $this->status == self::STATUS_NEW;
    }

    public function isPaused()
    {
        return $this->status == self::STATUS_PAUSED;
    }

    public function isRunning()
    {
        return $this->status == self::STATUS_RUNNING;
    }

    public function isCompleted()
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    public function isError()
    {
        return $this->status == self::STATUS_ERROR;
    }

    public function setCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function setRunning()
    {
        $this->status = self::STATUS_RUNNING;
        $this->save();
    }

    public function pause()
    {
        $this->status = self::STATUS_PAUSED;
        $this->save();
    }

    public function restart($subscription)
    {
        return $this->dispatchBulkVerificationJob($subscription);
    }

    public function setError($error, ?\Throwable $exception = null)
    {
        if ($error instanceof \Throwable && is_null($exception)) {
            $exception = $error;
            $error = $exception->getMessage();
        }

        $errorPayload = (string) $error;

        if (!is_null($exception)) {
            $traceLines = preg_split("/(\r\n|\n|\r)/", $exception->getTraceAsString());
            $traceTop = implode("\n", array_slice($traceLines, 0, 20));

            $errorPayload = sprintf(
                "Message: %s\nException: %s\nTop 20 trace lines:\n%s",
                (string) $error,
                get_class($exception),
                $traceTop
            );

            $this->logger()->error(sprintf(
                '[VerificationCampaign] Error: %s | Exception: %s\nTop 20 trace lines:\n%s',
                (string) $error,
                get_class($exception),
                $traceTop
            ));
        } else {
            $this->logger()->error(sprintf('[VerificationCampaign] Error: %s', (string) $error));
        }

        $this->status = self::STATUS_ERROR;
        $this->error = $errorPayload;
        $this->save();
    }

    public static function newDefault()
    {
        $verificationCampaign = new self();
        $verificationCampaign->type = VerificationCampaign::TYPE_UPLOAD;
        $verificationCampaign->status = VerificationCampaign::STATUS_NEW;

        return $verificationCampaign;
    }

    public static function createForCustomer(Customer $customer): self
    {
        $verificationCampaign = self::newDefault();
        $verificationCampaign->customer_id = $customer->id;
        $verificationCampaign->save();

        return $verificationCampaign;
    }

    public static function forCustomer(Customer $customer): Builder
    {
        return self::query()->where('customer_id', $customer->id);
    }

    public function dispatchBulkVerificationJob($subscription)
    {
        if (config('custom.dryrun')) {
            $this->logger()->info('- Running in DRYRUN mode, generating random results...');
            $sql = sprintf(
                "
                    UPDATE %s as s SET
                    s.verification_status = ELT(FLOOR(RAND()*4)+1, 'valid', 'invalid', 'unknown', 'risky'),
                    s.last_verification_at = CURRENT_TIMESTAMP(),
                    s.last_verification_by = 'DRYRUN',
                    s.last_verification_result = 'RAW',
                    s.updated_at = CURRENT_TIMESTAMP()
                    WHERE verification_campaign_id = %s
                ",
                table('email_addresses'),
                $this->id
            );

            DB::statement($sql);

            $this->setCompleted();

            return;
        }

        if ($this->isRunning()) {
            throw new Exception('Campaign already running');
        }

        $this->setRunning();

        $campaignId = (int) $this->id;
        $jobTypeName = static::JOB_TYPE_VERIFY_LIST;
        $job = new BulkVerifyOrchestrator($this, $subscription);

        return $this->dispatchWithBatchMonitor(
            $jobTypeName,
            $queue = ACM_QUEUE_TYPE_BATCH,
            [$job],
            $then = function ($batch) use ($campaignId) {
                $campaign = static::find($campaignId);

                if (!is_null($campaign)) {
                    $campaign->logger()->info(sprintf(
                        '[VerificationCampaign] Batch THEN callback reached. campaign_id=%s batch_id=%s',
                        $campaignId,
                        $batch->id
                    ));
                }

                if (!is_null($campaign) && !$campaign->isPaused()) {
                    $campaign->setCompleted();
                }
            },
            $catch = function ($batch, \Throwable $e) use ($campaignId) {
                $campaign = static::find($campaignId);

                if (!is_null($campaign)) {
                    $campaign->logger()->error(sprintf(
                        '[VerificationCampaign] Batch CATCH callback reached. campaign_id=%s batch_id=%s exception=%s message=%s',
                        $campaignId,
                        $batch->id,
                        get_class($e),
                        $e->getMessage()
                    ));
                }

                if (!is_null($campaign) && !$campaign->isPaused()) {
                    $campaign->setError($e);

                    $campaign->logger()->error(sprintf(
                        '[VerificationCampaign] End of CATCH callback. campaign_id=%s batch_id=%s',
                        $campaignId,
                        $batch->id
                    ));
                }
            },
            $finally = function ($batch) use ($campaignId) {
                $campaign = static::find($campaignId);

                if (is_null($campaign) || $campaign->isPaused()) {
                    return;
                }

                if ($campaign->isRunning()) {
                    $remaining = $campaign->emailAddresses()->new()->count();
                    if ($remaining === 0) {
                        $campaign->logger()->info(sprintf(
                            '[VerificationCampaign] Batch FINALLY completed campaign. campaign_id=%s batch_id=%s',
                            $campaignId,
                            $batch->id
                        ));
                        $campaign->setCompleted();
                    }
                }
            }
        );
    }

    public function emailAddresses()
    {
        return $this->hasMany(EmailAddress::class, 'verification_campaign_id');
    }

    public function getUnverifiedQuery(): Builder
    {
        return $this->emailAddresses()
            ->new()
            ->getQuery();
    }

    public function getVerifiedSubscribersPercentage(bool $cache = false): float
    {
        $total = $this->getSubscribersCount();
        if ($total == 0) {
            return 0.0;
        }

        return $this->getVerifiedSubscribersCount() / $total;
    }

    public function getVerifiedSubscribersCount(): int
    {
        return (int) $this->emailAddresses()->verified()->count();
    }

    public function getSubscribersCount(): int
    {
        return (int) $this->emailAddresses()->count();
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function updateVerificationResults(array $results, string $verificationBy): void
    {
        $tmpFields = [
            "`email` TEXT",
            "`status` TEXT",
            "`raw` TEXT",
        ];

        if (empty($results)) {
            $this->logger()->warning('- Empty verification results');
            return;
        }

        $sample = implode(', ', array_map(
            function ($record) {
                return $record['email'];
            },
            array_slice($results, 0, 4)
        ));

        $this->logger()->info('Updating verification results ('.sizeof($results).' records): '.$sample.'...');

        create_temp_db_table($tmpFields, $results, function ($tmpTable) {
            $nameWithPrefix = table($tmpTable);

            $sql = sprintf(
                "
                UPDATE %s as s INNER JOIN %s t ON s.email = t.email
                SET
                s.verification_status = t.status,
                s.last_verification_at = CURRENT_TIMESTAMP(),
                s.last_verification_by = %s,
                s.last_verification_result = t.raw,
                s.updated_at = CURRENT_TIMESTAMP()
                WHERE verification_campaign_id = %s
            ",
                table('email_addresses'),
                $nameWithPrefix,
                                db_quote($verificationBy),
                $this->id
            );

            DB::statement($sql);
        });
    }

    public function importFromArray(array $emails): void
    {
        // adding emails
        $records = array_map(function($email) {
            return [
                'uid' => uniqid(),
                'email' => $email,
                'customer_id' => $this->customer_id,
                    'verification_status' => VerificationStatus::NEW->value,
                'verification_campaign_id' => $this->id,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ];
        }, $emails);

        // insert
        EmailAddress::insert($records);
    }

    public function upload($file)
    {
        // save file name, for reference only, not used for processing
        // show to the web UI for reference
        $this->file_name = $file->getClientOriginalName();
        $this->save();

        // Get the file's path
        $path = $file->getPathname();

        // save email addresses
        list($file, $headerLine, $rows) = \App\Helpers\read_csv($path);

        //
        $emails = [];
        foreach($rows as $row) {
            $email = trim($row[0]);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }

        //
        $this->importFromArray($emails);
    }

    public function getRunningEmailAddress()
    {
        return $this->emailAddresses()
            ->new()
            ->first();
    }

    public function getVerifiedEmailAddresses()
    {
        return $this->emailAddresses()->verified()->get();
    }

    public function getProgress()
    {
        $total = $this->emailAddresses()->count();
        $verified = $this->emailAddresses()->verified()->count();

        $progress = $total == 0 ? 0 : (($verified / $total) * 100);

        return [
            'progress' => round($progress, 1),
            'total' =>  $total,
            'current' => $verified,
        ];
    }

    public function getResults()
    {
        return $this->emailAddresses()->verified()->get()->map(function($emailAddress) {
            return [
                'email' => $emailAddress->email,
                'status' => $emailAddress->verification_status,
                'score' => 'N/A',
            ];
        })->toArray();
    }

    public function exportFile()
    {
        $exportPath = storage_path("app/tmp/{$this->uid}-export.txt");
        
        //
        $records = $this->getResults();

        //
        $writer = Writer::createFromPath($exportPath, 'w+');

        $headers = [];
        $headers[] = 'EMAIL';
        $headers[] = 'RESULT';
        $headers[] = 'MX';
        $headers[] = 'ERROR';

        $writer->insertAll($records);

        return $exportPath;
    }

    public function logger(): LoggerInterface
    {
        if (!is_null($this->logger)) {
            return $this->logger;
        }

        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n");

        $logfile = $this->getLogFile();
        $stream = new RotatingFileHandler($logfile, 0, config('custom.log_level'));
        $stream->setFormatter($formatter);

        $pid = getmypid();
        $logger = new Logger($pid);
        $logger->pushHandler($stream);
        $this->logger = $logger;

        return $this->logger;
    }

    public function getLogFile()
    {
        $path = storage_path(join_paths('logs', php_sapi_name(), '/verification-campaign-'.$this->uid.'.log'));
        return $path;
    }
}
