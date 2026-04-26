<?php

namespace Acelle\Server\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use League\Csv\Writer;
use Acelle\Server\Model\EmailAddress;
use Acelle\Server\Model\VerificationCampaign;
use Exception;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('server::landing_page.index'); 
    } 
    
    public function verify(Request $request)
    {
        if ($request->isMethod('post')) {
            $emailAddress = EmailAddress::publicVerify($request->email);
            return view('server::validate.verifyResult', [
                'emailAddress' => $emailAddress,
                'mxs' => $emailAddress->getResult()['mxs'] ?? null,
            ]); 
        }

        return view('server::landing_page.verify'); 
    }

    public function progress(Request $request)
    {
        $verificationCampaign = VerificationCampaign::findByUid($request->file_name);

        return view('server::landing_page.progress', [
            'file_name' => $verificationCampaign->uid,
        ]);
    }

    public function upload(Request $request)
    {
        $verificationCampaign = VerificationCampaign::newDefault();

        $user = $request->user();
        if (is_null($user) || is_null($user->customer)) {
            throw new Exception('No authenticated customer', 1);
        }

        $subscription = $user->customer->getCurrentActiveSubscription();
        if (is_null($subscription)) {
            throw new Exception('No active subscription', 1);
        }

        // upload
        $verificationCampaign->upload($request->file('file'));

        // run
        $verificationCampaign->dispatchBulkVerificationJob($subscription);

        // redirect
        return view('server::landing_page.progress', [
            'file_name' =>  $verificationCampaign->uid,
        ]);
    }

    public function progressJson(Request $request)
    {
        $verificationCampaign = VerificationCampaign::findByUid($request->file_name);

        return response()->json(array_merge($verificationCampaign->getProgress(), [
            'status' => view('server::validate._status', [
                'verificationCampaign' => $verificationCampaign,
            ])->render()
        ]));
    }

    public function report(Request $request)
    {
        $verificationCampaign = VerificationCampaign::findByUid($request->file_name);

        $exportFilePath = $verificationCampaign->exportFile();

        return response()->download($exportFilePath);
    }
}