<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
                // 'wpMessage'  => 'required',
            ]);

            if ($validator->fails()) 
            {
                return back()->with('error_message', 'Star Marked Fields Are Required With Proper Data !!!');
            }
            else
            {
                $params = [
                    'apikey' => "983676c03c1876759052b25e388271ac" ,
                    'mobile' => strip_tags($request->whatsappNo) ,
                ];

                if(!empty($request->wpMessage))
                {
                    $params['msg'] = strip_tags($request->wpMessage);
                }

                if(!empty($request->file('wpImage')))
                {
                    $wpImageFile = $request->file('wpImage');
                    $wpImageExt = $wpImageFile->getClientOriginalExtension();
                    if($wpImageExt != 'jpg' && $wpImageExt != 'jpeg' && $wpImageExt != 'png' && $wpImageExt != 'webp')
                    {
                        return back()->with('error_message', 'Please upload a valid image file !!!');
                    }
                    

                    $wpImageName = time() . "_" . strip_tags($wpImageFile->getClientOriginalName());

                    // inside storage/app/public/wp-images/
                    $path = $wpImageFile->storeAs('wp-images', $wpImageName, 'public');

                    $imageUrl = asset('storage/' . $path);

                    $params['img1'] = $imageUrl;

                    // dd($imageUrl);
                }

              
                try 
                {
                    // Call the API
                    $response = Http::get('https://demo.digitalsms.biz/api/', $params);
                    $result = $response->json();
                    // dd($result);

                    if (!empty($path)) // delete uploaded image if exists
                    {
                        Storage::disk('public')->delete($path); 
                    }

                    if ($result && isset($result['status']) && $result['status'] == 1) 
                    {
                        return back()->with('success_message', 'WhatsApp message sent successfully !!!');
                    } 
                    else 
                    {
                        return back()->with('error_message', 'Failed to send WhatsApp message !!!');
                    }
                } 
                catch (\Exception $e)
                {
                    return back()->with('error_message', 'Exception: ' . $e->getMessage());
                }
                
                
           
            }

        }


        $page_name = 'whatsapp.wp-message';

        return view('maincontents.' . $page_name);
    }
}
