<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class WpApiController extends Controller
{
    protected $data;

    public function __construct()
    {
        $this->data = [
            'title'             => 'WP Message',
            'controller'        => 'WpApiController',
            'controller_route'  => 'wp-message',
        ];
    }

    public function wpMessage(Request $request)
    {
        if ($request->isMethod('post')) 
        {
            $validator = Validator::make($request->all(), [
                'name'       => 'required',
                'whatsappNo' => 'required|digits:10',
            ]);

            if ($validator->fails()) 
            {
                return back()->with('error_message', 'All Fields Are Required With Proper Data !!!');
            }
            else
            {
                $mobile   = $request->whatsappNo; 
                $apiKey   = "983676c03c1876759052b25e388271ac"; 
                $msg      = "Test Message";
    
                // Call the API
                $response = Http::get("https://demo.digitalsms.biz/api/", [
                    'apikey' => strip_tags($apiKey),
                    'mobile' => strip_tags($mobile),
                    'msg'    => strip_tags($msg),
                ]);
    
                // dd($response->body());
    
                $result = $response->json();
    
                if ($result && isset($result['status']) && $result['status'] == 1) 
                {
                    return back()->with('success_message', 'WhatsApp message sent successfully !!!');
                } else {
                    return back()->with('error_message', 'Failed to send WhatsApp message !!!');
                }
            }

        }


        $page_name = 'whatsapp.wp-message';

        return view('maincontents.' . $page_name);
    }
}
