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
                return redirect()->back()->with('error_message', 'Star Marked Fields Are Required With Proper Data !!!');
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
                    if(count($request->file('wpImage')) <= 4)
                    {
                        $count = 1;
                        $wpImageName_Arr = [];
                        foreach($request->file('wpImage') as $eachFile)
                        {
                            // $wpImageFile = $request->file('wpImage');
                            $wpImageFile = $eachFile;
                            $wpImageExt = $wpImageFile->getClientOriginalExtension();
                            if($wpImageExt != 'jpg' && $wpImageExt != 'jpeg' && $wpImageExt != 'png' && $wpImageExt != 'webp')
                            {
                                return redirect()->back()->with('error_message', 'Please upload a valid image file !!!');
                            }
                            
        
                            // $wpImageName = time() . "_" . strip_tags($wpImageFile->getClientOriginalName());
                            $wpImageName = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $wpImageFile->getClientOriginalName());
        
                            
                            // into /public/wp-images/
                            $wpImageFile->move(public_path('wp-images'), $wpImageName);
                            
                            // $imageUrl = asset('wp-images/' . $wpImageName);
                            $imageUrl = asset('public/wp-images/' . $wpImageName);
        
                            // dd($imageUrl);
                            $key = 'img' . $count;
    
                            $params[$key] = $imageUrl;
                            
                            $wpImageName_Arr[] = $wpImageName;
    
                            $count++;
                        }
                    }
                    else
                    {
                        return redirect()->back()->with('error_message', 'Maximum 4 images allowed !!!');
                    }

                    // handling problem: if we select multiple images without message then it's taking an empty string 
                    if(empty($request->wpMessage))
                    {
                        unset($params['msg']);
                    }
                    
                }

                // dd($params);

                try 
                {
                    // Call the API
                    $response = Http::get('https://demo.digitalsms.biz/api/', $params);
                    $result = $response->json();

                    // dd($result);

                    if (!empty($wpImageName_Arr)) // delete uploaded image if exists
                    {
                        foreach($wpImageName_Arr as $eachFileName)
                        {
                            $filePath = public_path('wp-images/' . $eachFileName);
                        
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                        }
                    }

                    if ($result && isset($result['status']) && $result['status'] == 1) 
                    {
                        return redirect()->back()->with('success_message', 'WhatsApp message sent successfully !!!');
                    } 
                    else 
                    {
                        return redirect()->back()->with('error_message', 'Failed to send WhatsApp message !!!');
                    }
                } 
                catch (\Exception $e)
                {
                    return redirect()->back()->with('error_message', 'Exception: ' . $e->getMessage());
                }
                
                
           
            }

        }


        $page_name = 'whatsapp.wp-message';

        return view('maincontents.' . $page_name);
    }
}
