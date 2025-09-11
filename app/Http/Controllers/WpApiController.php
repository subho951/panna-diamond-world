<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        if ($request->isMethod('post')) {
            $request->validate([
                'name'       => 'required|string|max:100',
                'whatsappNo' => 'required|digits:10',
            ]);

            $mobile   = "91" . $request->whatsappNo; // prefix country code (India = 91)
            $apiKey   = "983676c03c1876759052b25e388271ac"; 
            $msg      = "testmsg";

            // Call the API
            $response = Http::get("https://demo.digitalsms.biz/api/", [
                'apikey' => $apiKey,
                'mobile' => $mobile,
                'msg'    => $msg,
            ]);

            $result = $response->json();

            if ($result && isset($result['status']) && $result['status'] == 1) 
            {
                return back()->with('success', 'WhatsApp message sent successfully!');
            } else {
                return back()->with('error', 'Failed to send WhatsApp message.');
            }
        }

        $title     = $this->data['title'];
        $page_name = 'whatsapp.wp-message';

        return view('maincontents.' . $page_name, [
            'module' => $this->data,
        ]);
    }
}
