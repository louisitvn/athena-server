<?php

namespace Acelle\Server\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Acelle\Server\Model\EmailAddress;
use Acelle\Server\Model\VerificationCampaign;
use Illuminate\Support\Facades\Cache;
use App\Model\ApiKey;

class MonitorController extends Controller
{
    public function list(Request $request)
    {
        $emailAddresses = EmailAddress::forCustomer($request->user()->customer)->verified()->search($request);

        if ($request->tab) {
            $emailAddresses = $emailAddresses->whereVerificationStatus($request->tab);
        }
        
        $emailAddresses = $emailAddresses->orderBy($request->sort_order ?? 'created_at', $request->sort_direction ?? 'desc');

        $emailAddresses = $emailAddresses->paginate($request->per_page);

        return view('server::monitor.list', [
            'emailAddresses' => $emailAddresses,
        ]);
    }

    public function index(Request $request)
    {
        $perPage    =   $request->perPage ?? 5;
        $page       =   $request->page ?? 1;
        $keyword    =   $request->keyword ?? '' ;
        $view       =   $request->view ?? '' ;
        $status     =   $request->status ?? '' ;

        return view('server::monitor.index', [ 
            'keyword'       => $keyword,
            'view'          => $view,
            'perPage'       => $perPage,
            'page'          => $page,
            'status'        => $status,
        ]); 
    }
}