<?php

namespace Saidtech\Zpay\Http\Controllers;

use Saidtech\Zpay\Zpay;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ZpayController extends Controller
{
    /**
     * Return an empty response simply to trigger the storage of the CSRF cookie in the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $response  = Zpay::getSession();

        return view('zpay::base', ['body' => $response]);
    }

    public function pay(Request $request)
    {
        $payload = [
            'mobile' => $request->session,
            'price' => $request->price,
            'currency' => $request->currency,
        ];
        
        $response = Zpay::sendTransaction($payload, $request->session, 'c2b');

        return $response;
    }
}
