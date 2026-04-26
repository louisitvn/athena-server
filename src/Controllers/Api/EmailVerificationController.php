<?php

namespace Acelle\Server\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Plans\Credits\CreditsService;
use Illuminate\Http\Request;
use Acelle\Server\Model\EmailAddress;
use Acelle\Server\Model\VerificationCampaign;
use Illuminate\Support\Facades\Cache;
use App\Model\ApiKey;
use Athena\EmailVerificationResult;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $apiKey = ApiKey::where('key', $request->api_key)->first();
        $email = $request->email;

        // check if customer exists
        if (!checkEmail($email)) {
            return response()->json(array('status' => 0, 'message' => 'Email is not valid'), 404);
        }

        $customer = $apiKey->customer;
        $currentSubscription = $customer->getCurrentActiveSubscription();

        if (!$currentSubscription) {
            throw new \Exception("No active subscription. Please check your subscription.", 1);
        }

        $creditTracker = app(CreditsService::class)->verifyEmailTracker($currentSubscription);

        // create new emailAddress from api key
        $emailAddress = EmailAddress::createFromApiKey($email, $apiKey);

        // @todo: do not count here, find a consistent way to count it
        $creditTracker->count();

        $engine = new \Acelle\Server\Library\AthenaEngine();
        list($status, $raw) = $engine->verifySingle($email);
        $rawJson = json_decode($raw, true);

        // verify that the email address
        $emailAddress->verification_status = $status;
        $emailAddress->last_verification_at = now();
        $emailAddress->last_verification_by = 'AthenaEvs';
        $emailAddress->last_verification_result = $raw;
        $emailAddress->save();

        //
        $result = new EmailVerificationResult([
            'status' => $emailAddress->verification_status,
            'mxs' => $rawJson['mxs'],

            "result" => $emailAddress->getResult(),
            "accept_all" => null,
            "did_you_mean" => null,
            "disposable" => false,
            "domain" => count(explode('@', $email)) > 1 ? explode('@', $email)[1] : null,
            "duration" => null,
            "email" => $email,
            "first_name" => null,
            "free" => null,
            "full_name" => null,
            "gender" => null,
            "last_name" => null,
            "mailbox_full" => false,
            "mx_record" => null,
            "no_reply" => false,
            "reason"  => $emailAddress->verification_status,
            "role" => false,
            "score" => 100,
            "smtp_provider" => count(explode('@', $email)) > 1 ? explode('@', $email)[1] : null,
            "state" => $emailAddress->verification_status,
            "tag" => null,
            "user" => null,
        ]);

        return response()->json($result->toArray());


        // return response()->json([
        //     'status' => $emailAddress->verification_status,
        //     'mxs' => [], // todo



        //     'result' => $emailAddress->getResult(),
        //     "accept_all" => null,
        //     "did_you_mean" => null,
        //     "disposable" => false,
        //     "domain" => count(explode('@', $email)) > 1 ? explode('@', $email)[1] : null,
        //     "duration" => null,
        //     "email" => $email,
        //     "first_name" => null,
        //     "free" => null,
        //     "full_name" => null,
        //     "gender" => null,
        //     "last_name" => null,
        //     "mailbox_full" => false,
        //     "mx_record" => null,
        //     "no_reply" => false,
        //     "reason"  => $emailAddress->verification_status,
        //     "role" => false,
        //     "score" => 100,
        //     "smtp_provider" => count(explode('@', $email)) > 1 ? explode('@', $email)[1] : null,
        //     "state" => $emailAddress->verification_status,
        //     "tag" => null,
        //     "user" => null,
        // ], 200);
    }

    public function batchVerify(Request $request)
    {
        $apiKey = ApiKey::where('key', $request->api_key)->first();

        $currentSubscription = $apiKey->customer->getCurrentActiveSubscription();

        if (!$currentSubscription) {
            return response()->json(array('message' => 'No active subscription', 403));
        }

        $emails = $request->emails;

        // Check if the input is a string
        if (is_string($emails)) {
            // Split the string into an array using both ',' and ';' as delimiters
            $emails = preg_split('/[\s,;]+/', $emails);

            // Remove any empty elements that may result from multiple delimiters
            $emails = array_filter($emails, function($value) {
                return !empty($value);
            });
        }

        $emails = array_filter(array_map('trim', $emails));

        // Filter the array to keep only valid emails
        $emails = array_filter($emails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        });

        // Create campaign
        $verificationCampaign = VerificationCampaign::createForCustomer($apiKey->customer);
            
        // upload
        $verificationCampaign->importFromArray($emails);

        $verificationCampaign->dispatchBulkVerificationJob($currentSubscription);

        return response()->json([
            'status' => 1,
            'batch_id' => $verificationCampaign->uid,
        ]);
    }

    public function batchStatus(Request $request)
    {
        $apiKey = ApiKey::where('key', $request->api_key)->first();
        
        // check if customer exists
        if (!$request->batch_id) {
            return response()->json(array( 'message' => 'No batch_id provided!'), 404);
        }

        $verificationCampaign = VerificationCampaign::forCustomer($apiKey->customer)->whereUid($request->batch_id)->first();

        // check if customer exists
        if (!$verificationCampaign) {
            return response()->json(array('message' => 'No campaign found with the batch_id: ' . $request->batch_id), 404);
        }

        return response()->json([
            'status' => $verificationCampaign->isCompleted(),
            'batch_id' => $verificationCampaign->uid,
        ]);
    }

    public function batchResult(Request $request)
    {
        $apiKey = ApiKey::where('key', $request->api_key)->first();

        // check if customer exists
        if (!$request->batch_id) {
            return response()->json(array('status' => 0, 'message' => 'No batch_id provided!'), 404);
        }

        $verificationCampaign = VerificationCampaign::forCustomer($apiKey->customer)->whereUid($request->batch_id)->first();

        // check if customer exists
        if (!$verificationCampaign) {
            return response()->json(array('status' => 0, 'message' => 'No campaign found with the batch_id: ' . $request->batch_id), 404);
        }

        return response()->json([
            'status' => 1,
            'batch_id' => $verificationCampaign->uid,
            'result' => $verificationCampaign->getResults(),
        ]);
    }

    public function getCredits(Request $request)
    {
        $apiKey = ApiKey::where('key', $request->api_key)->first();
        $customer = $apiKey->customer;

        if (!$customer->getCurrentActiveSubscription()) {
            return response()->json(array('status' => 0, 'message' => 'The customer does not have an active subscription!'), 404);
        }

        return [
            'credits' => app(CreditsService::class)->verifyEmailTracker($customer->getCurrentActiveSubscription())->getRemainingCredits(),
        ];
    }

    public function test(Request $request)
    {
        return response()->json(['status' => 'ok'], 200);
    }
}
