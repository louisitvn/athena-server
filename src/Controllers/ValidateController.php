<?php

namespace Acelle\Server\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Plans\Credits\CreditsService;
use Illuminate\Http\Request;
use Acelle\Server\Model\EmailAddress;
use Acelle\Server\Model\VerificationCampaign;
use Illuminate\Support\Facades\Cache;
use App\Model\ApiKey;
use Exception;

class ValidateController extends Controller
{
    public function index(Request $request)
    {
        $perPage    =   $request->perPage ?? 5;
        $page       =   $request->page ?? 1;
        $keyword    =   $request->keyword ?? '' ;
        $view       =   $request->view ?? '' ;
        $status     =   $request->status ?? '' ;

        $emailAddresses = EmailAddress::all();

        return view('server::validate.index', [ 
            'keyword'       => $keyword,
            'view'          => $view,
            'perPage'       => $perPage,
            'page'          => $page,
            'status'        => $status,
            'emailAddresses' => $emailAddresses,
        ]); 
    }

    public function list(Request $request)
    {
        $verificationCampaigns = VerificationCampaign::forCustomer($request->user()->customer)
            ->search($request->keyword)
            ->orderBy($request->sort_order ?? 'created_at', $request->sort_direction ?? 'desc');

        $verificationCampaigns = $verificationCampaigns->paginate($request->per_page);

        return view('server::validate.list', [
            'verificationCampaigns' => $verificationCampaigns,
        ]);
    }

    public function emailsList(Request $request)
    {
        $emailAddresses = EmailAddress::search($request)
            ->orderBy($request->sort_order ?? 'created_at', $request->sort_direction ?? 'desc');

        $emailAddresses = $emailAddresses->paginate($request->per_page);

        return view('server::validate.emailsList', [
            'emailAddresses' => $emailAddresses,
        ]);
    }

    public function validate_list()
    {
        return view('server::validate.validate_list', [
            ''=>''
        ]);
    }
    public function validate_bulk()
    {
        return view('server::validate.validate_bulk', [ ''=>''
        
        ]); 
    }
    public function validate_listing(Request $request)
    {

    }

    public function verify(Request $request)
    {
        if ($request->isMethod('post')) {
            $customer = $request->user()->customer;
            $currentSubscription = $customer->getCurrentActiveSubscription();

            if (!$currentSubscription) {
                throw new Exception("No active subscription", 1);
            }

            $creditTracker = app(CreditsService::class)->verifyEmailTracker($currentSubscription);

            try {
                $emailAddress = EmailAddress::verifyByCustomer($request->email, $request->user()->customer);

                // @TODO: handle credit count in a transaction
                $creditTracker->count();
            } catch (\App\Library\Exception\OutOfCredits $e) {
                return view('server::validate.verifyError', [
                    'message' => trans('server::messages.quota.error.credits_exceeded')
                ]);

            } catch (\Throwable $e) {
                return view('server::validate.verifyError', [
                    'message' => $e->getMessage(),
                ]);
            }

            return view('server::validate.verifyResult', [
                'emailAddress' => $emailAddress,
                'mxs' => $emailAddress->getResult()['mxs'] ?? [],
            ]); 
        }

        return view('server::validate.verify'); 
    }

    public function upload(Request $request)
    {
        $customer = $request->user()->customer;
        $currentSubscription = $customer->getCurrentActiveSubscription();

        if (!$currentSubscription) {
            throw new Exception("No active subscription", 1);
        }

        $verificationCampaign = VerificationCampaign::createForCustomer($request->user()->customer);
            
        // upload
        $verificationCampaign->upload($request->file('file'));

        $verificationCampaign->dispatchBulkVerificationJob($currentSubscription);

        // progress
        return view('server::validate.progress', [
            'verificationCampaign' => $verificationCampaign,
        ]);
    }

    public function import()
    {
        return redirect()->route('acelle_server.validate.index');
    }

    public function validateBulkSave(Request $request)
    {
        $customer = $request->user()->customer;


        $currentSubscription = $customer->getCurrentActiveSubscription();

        if (!$currentSubscription) {
            throw new Exception("No active subscription", 1);
        }

        $emails = explode(PHP_EOL, $request->emails);
        $emails = array_filter(array_map('trim', $emails));

        // Filter the array to keep only valid emails
        $emails = array_filter($emails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        });

        // upload
        $limit = 10;
        $rateInMinutes = 5;

        // Limit number of emails per bulk check
        $emails = array_slice($emails, 0, $limit);

        // Enforce rate limit
        $bulkRateKey = "last-bulk-check-{$customer->uid}";
        $lastBulkCheck = Cache::get($bulkRateKey);
        $availableAt = ($lastBulkCheck) ? $lastBulkCheck->addMinutes($rateInMinutes) : null;
        $now = $customer->getCurrentTime();

        if ($availableAt && $availableAt->gt($now) ) {
            $error = trans('em.validation.bulk.timeout.error', [
                'time' => $availableAt->diffForHumans(),
            ]);
            throw new Exception($error);
        } else {
            Cache::put($bulkRateKey, $now);
        }

        // Create campaign
        $verificationCampaign = VerificationCampaign::createForCustomer($request->user()->customer);

        $verificationCampaign->importFromArray($emails);

        // start
        $verificationCampaign->logger()->info('Starting...');
        $verificationCampaign->dispatchBulkVerificationJob($currentSubscription);
        
        return view('server::validate.bulkResult', [
            'verificationCampaign' => $verificationCampaign,
            'emailAddresses' => $verificationCampaign->getVerifiedEmailAddresses(),
            'runningEmailAddress' => $verificationCampaign->getRunningEmailAddress(),
        ]); 
    }

    public function validateBulkResult(Request $request, $verification_campaign_uid)
    {
        $verificationCampaign = VerificationCampaign::findByUid($verification_campaign_uid);

        return view('server::validate.bulkResult', [
            'verificationCampaign' => $verificationCampaign,
            'emailAddresses' => $verificationCampaign->getVerifiedEmailAddresses(),
            'runningEmailAddress' => $verificationCampaign->getRunningEmailAddress(),
        ]); 
    }

    public function progressJson(Request $request, $verification_campaign_uid)
    {
        $verificationCampaign = VerificationCampaign::findByUid($verification_campaign_uid);

        return response()->json(array_merge($verificationCampaign->getProgress(), [
            'status' => view('server::validate._status', [
                'verificationCampaign' => $verificationCampaign,
            ])->render()
        ]));
    }

    public function fullProgress(Request $request, $uid)
    {
        $verificationCampaign = VerificationCampaign::findByUid($uid);

        return view('server::validate.full_progress', [
            'verificationCampaign' => $verificationCampaign,
        ]);
    }

    public function fullProgressJson(Request $request, $uid)
    {
        $campaign = VerificationCampaign::findByUid($uid);

        $total = $campaign->emailAddresses()->count();

        $statusCounts = $campaign->emailAddresses()
            ->selectRaw('verification_status, COUNT(*) as count')
            ->groupBy('verification_status')
            ->pluck('count', 'verification_status')
            ->toArray();

        $serverRows = $campaign->emailAddresses()
            ->whereNotNull('last_verification_by')
            ->selectRaw('last_verification_by, COUNT(*) as count')
            ->groupBy('last_verification_by')
            ->orderByDesc('count')
            ->get();

        $pending  = (int)($statusCounts[\Acelle\Server\Library\VerificationStatus::NEW->value] ?? 0);
        $verified = $total - $pending;
        $percent  = $total > 0 ? round(($verified / $total) * 100, 1) : 0;

        $monitor = $campaign->jobMonitors()
            ->byJobType(VerificationCampaign::JOB_TYPE_VERIFY_LIST)
            ->latest()
            ->first();

        $batch = $monitor?->getBatch();
        $batchInfo = null;
        if ($batch) {
            $batchInfo = [
                'id'             => $batch->id,
                'total_jobs'     => $batch->totalJobs,
                'pending_jobs'   => $batch->pendingJobs,
                'failed_jobs'    => $batch->failedJobs,
                'processed_jobs' => $batch->processedJobs(),
                'progress'       => $batch->progress(),
                'finished'       => $batch->finished(),
                'cancelled'      => $batch->cancelled(),
                'created_at'     => $batch->createdAt?->toISOString(),
                'finished_at'    => $batch->finishedAt?->toISOString(),
            ];
        }

        return response()->json([
            'campaign' => [
                'status'     => $campaign->status,
                'file_name'  => $campaign->file_name,
                'created_at' => $campaign->created_at?->toISOString(),
                'updated_at' => $campaign->updated_at?->toISOString(),
                'error'      => $campaign->error,
            ],
            'status_html' => view('server::validate._status', ['verificationCampaign' => $campaign])->render(),
            'progress' => [
                'total'          => $total,
                'verified'       => $verified,
                'pending'        => $pending,
                'percent'        => $percent,
                'deliverable'    => (int)($statusCounts['deliverable'] ?? 0),
                'undeliverable'  => (int)($statusCounts['undeliverable'] ?? 0),
                'unknown'        => (int)($statusCounts['unknown'] ?? 0),
                'risky'          => (int)($statusCounts['risky'] ?? 0),
            ],
            'servers'    => $serverRows->map(fn($r) => [
                'name'  => $r->last_verification_by,
                'count' => (int)$r->count,
            ])->values(),
            'batch'      => $batchInfo,
            'fetched_at' => now()->toISOString(),
        ]);
    }

    public function fullProgressEmails(Request $request, $uid)
    {
        $campaign = VerificationCampaign::findByUid($uid);

        $query = $campaign->emailAddresses();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('verification_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $allowed = ['email', 'verification_status', 'last_verification_at', 'id'];
        $sortBy  = in_array($request->sort_by, $allowed) ? $request->sort_by : 'id';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';

        $emails = $query->orderBy($sortBy, $sortDir)->paginate((int)($request->per_page ?? 25));

        return view('server::validate._full_progress_emails', [
            'emails'   => $emails,
            'campaign' => $campaign,
        ]);
    }

    public function report(Request $request, $verification_campaign_uid)
    {
        $verificationCampaign = VerificationCampaign::findByUid($verification_campaign_uid);

        $exportFilePath = $verificationCampaign->exportFile();

        return response()->download($exportFilePath);
    }

    public function start(Request $request, $uid)
    {
        $customer = $request->user()->customer;
        $currentSubscription = $customer->getCurrentActiveSubscription();
        $verificationCampaign = VerificationCampaign::findByUid($uid);

        //
        $verificationCampaign->dispatchBulkVerificationJob($currentSubscription);
    }

    public function restart(Request $request, $uid)
    {
        $customer = $request->user()->customer;
        $currentSubscription = $customer->getCurrentActiveSubscription();
        $verificationCampaign = VerificationCampaign::findByUid($uid);

        //
        $verificationCampaign->restart($currentSubscription);
    }

    public function progress(Request $request, $uid)
    {
        $verificationCampaign = VerificationCampaign::findByUid($uid);

        // progress
        return view('server::validate.progress', [
            'verificationCampaign' => $verificationCampaign,
        ]);
    }

    public function delete(Request $request, $uid)
    {
        $verificationCampaign = VerificationCampaign::findByUid($uid);

        $verificationCampaign->delete();
    }

    public function pause(Request $request, $uid)
    {
        $verificationCampaign = VerificationCampaign::findByUid($uid);

        $verificationCampaign->pause();
    }

    public function emails(Request $request)
    {
        $perPage    =   $request->perPage ?? 5;
        $page       =   $request->page ?? 1;
        $keyword    =   $request->keyword ?? '' ;
        $view       =   $request->view ?? '' ;
        $status     =   $request->status ?? '' ;

        $emailAddresses = EmailAddress::all();

        return view('server::validate.emails', [ 
            'keyword'       => $keyword,
            'view'          => $view,
            'perPage'       => $perPage,
            'page'          => $page,
            'status'        => $status,
            'emailAddresses' => $emailAddresses,
        ]); 
    }

    public function credits(Request $request)
    {
        $customer = $request->user()->customer;

        echo 123;
    }
}