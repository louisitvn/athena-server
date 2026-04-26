<?php

namespace Acelle\Server\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Plans\Credits\CreditsService;
use Illuminate\Http\Request;
use Acelle\Server\Model\EmailAddress;
use Acelle\Server\Model\VerificationCampaign;
use Athena\EmailVerificationResult;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $email = $request->email;

        if (!checkEmail($email)) {
            return response()->json(['status' => 0, 'message' => 'Email is not valid'], 422);
        }

        $customer = $request->user()->customer;
        if (is_null($customer)) {
            return response()->json(['status' => 0, 'message' => 'Authenticated user has no customer'], 403);
        }

        $currentSubscription = $customer->getCurrentActiveSubscription();
        if (!$currentSubscription) {
            return response()->json(['status' => 0, 'message' => 'No active subscription'], 403);
        }

        $creditTracker = app(CreditsService::class)->verifyEmailTracker($currentSubscription);

        $emailAddress = EmailAddress::createForCustomer($email, $customer);

        // @todo: do not count here, find a consistent way to count it
        $creditTracker->count();

        $engine = new \Acelle\Server\Library\AthenaEngine();
        list($status, $raw) = $engine->verifySingle($email);
        $rawJson = json_decode($raw, true);

        $emailAddress->verification_status = $status;
        $emailAddress->last_verification_at = now();
        $emailAddress->last_verification_by = 'AthenaEngine';
        $emailAddress->last_verification_result = $raw;
        $emailAddress->save();

        $domain = count(explode('@', $email)) > 1 ? explode('@', $email)[1] : null;

        $result = new EmailVerificationResult([
            'status'        => $emailAddress->verification_status,
            'mxs'           => $rawJson['mxs'] ?? [],
            'result'        => $emailAddress->getResult(),
            'accept_all'    => null,
            'did_you_mean'  => null,
            'disposable'    => false,
            'domain'        => $domain,
            'duration'      => null,
            'email'         => $email,
            'first_name'    => null,
            'free'          => null,
            'full_name'     => null,
            'gender'        => null,
            'last_name'     => null,
            'mailbox_full'  => false,
            'mx_record'     => null,
            'no_reply'      => false,
            'reason'        => $emailAddress->verification_status,
            'role'          => false,
            'score'         => 100,
            'smtp_provider' => $domain,
            'state'         => $emailAddress->verification_status,
            'tag'           => null,
            'user'          => null,
        ]);

        return response()->json($result->toArray());
    }

    public function batchVerify(Request $request)
    {
        $customer = $request->user()->customer;
        if (is_null($customer)) {
            return response()->json(['status' => 0, 'message' => 'Authenticated user has no customer'], 403);
        }

        $currentSubscription = $customer->getCurrentActiveSubscription();
        if (!$currentSubscription) {
            return response()->json(['status' => 0, 'message' => 'No active subscription'], 403);
        }

        $emails = $request->emails;

        if (is_string($emails)) {
            $emails = preg_split('/[\s,;]+/', $emails);
            $emails = array_filter($emails, fn ($v) => !empty($v));
        }

        $emails = array_filter(array_map('trim', $emails ?? []));
        $emails = array_filter($emails, fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL) !== false);

        $verificationCampaign = VerificationCampaign::createForCustomer($customer);
        $verificationCampaign->importFromArray($emails);
        $verificationCampaign->dispatchBulkVerificationJob($currentSubscription);

        return response()->json([
            'status'   => 1,
            'batch_id' => $verificationCampaign->uid,
        ]);
    }

    public function batchStatus(Request $request)
    {
        if (!$request->batch_id) {
            return response()->json(['status' => 0, 'message' => 'No batch_id provided'], 422);
        }

        $customer = $request->user()->customer;
        if (is_null($customer)) {
            return response()->json(['status' => 0, 'message' => 'Authenticated user has no customer'], 403);
        }

        $verificationCampaign = VerificationCampaign::forCustomer($customer)->whereUid($request->batch_id)->first();
        if (!$verificationCampaign) {
            return response()->json(['status' => 0, 'message' => 'No campaign found with batch_id: '.$request->batch_id], 404);
        }

        return response()->json([
            'status'   => $verificationCampaign->isCompleted(),
            'batch_id' => $verificationCampaign->uid,
        ]);
    }

    public function batchResult(Request $request)
    {
        if (!$request->batch_id) {
            return response()->json(['status' => 0, 'message' => 'No batch_id provided'], 422);
        }

        $customer = $request->user()->customer;
        if (is_null($customer)) {
            return response()->json(['status' => 0, 'message' => 'Authenticated user has no customer'], 403);
        }

        $verificationCampaign = VerificationCampaign::forCustomer($customer)->whereUid($request->batch_id)->first();
        if (!$verificationCampaign) {
            return response()->json(['status' => 0, 'message' => 'No campaign found with batch_id: '.$request->batch_id], 404);
        }

        return response()->json([
            'status'   => 1,
            'batch_id' => $verificationCampaign->uid,
            'result'   => $verificationCampaign->getResults(),
        ]);
    }

    public function getCredits(Request $request)
    {
        $customer = $request->user()->customer;
        if (is_null($customer)) {
            return response()->json(['status' => 0, 'message' => 'Authenticated user has no customer'], 403);
        }

        $sub = $customer->getCurrentActiveSubscription();
        if (!$sub) {
            return response()->json(['status' => 0, 'message' => 'No active subscription'], 403);
        }

        return response()->json([
            'credits' => app(CreditsService::class)->verifyEmailTracker($sub)->getRemainingCredits(),
        ]);
    }

    public function test(Request $request)
    {
        return response()->json(['status' => 'ok'], 200);
    }
}
