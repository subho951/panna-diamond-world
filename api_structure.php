<?php

/* Global Search */
    public function globalSearch(Request $request)
    {
        $apiStatus          = TRUE;
        $apiMessage         = '';
        $apiResponse        = [];
        $apiExtraField      = '';
        $apiExtraData       = '';

        $headerData         = $request->header();
        $requestData        = $request->all();
        $requiredFields     = [
            'key'          => 'required',
            'source'       => 'required',
            'page_no'      => 'required', 
            'per_page'     => 'required', 
            'search_text'  => 'required',
        ];
        
        $validator = Validator::make($requestData, $requiredFields);
        if($validator->fails())
        {
            $apiStatus          = FALSE;
            $apiMessage         = 'All Data Are Not Present !!!';
        }
        else
        {
            // $apiStatus          = TRUE;
            // $apiMessage         = 'All Data Are Present !!!';
            
            if($headerData['key'][0] == env('PROJECT_KEY'))
            {
                $app_access_token           = $headerData['authorization'][0];
                $getTokenValue              = $this->tokenAuth($app_access_token);
                if($getTokenValue['status'])
                {
                    $uId                    = $getTokenValue['data'][1];
                    $expiry                 = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                    $getUser                = User::where('id', '=', $uId)->first();

                    $page_no                = $requestData['page_no'];
                    $per_page               = $requestData['per_page'];
                    $search_text            = $requestData['search_text'];

                    if(!empty($getUser))
                    {
                        // logic start
                        $branch_id                      = $getUser->branch_id;
                        $assigned_telecaller_id         = $uId;
                        $limit                          = $per_page; // per page elements
                        if($page_no == 1){
                            $offset         = 0;
                        } else {
                            $offset         = (($limit * $page_no) - $limit); // ((15 * 3) - 15)
                        }



                        
                        // logic end

                        $apiStatus          = TRUE;
                        http_response_code(200);
                        $apiMessage         = 'Data Available !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                    else 
                    {
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'User Not Found !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }

                }
                else 
                {
                    http_response_code(200);
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                    $apiStatus          = FALSE;
                    $apiMessage         = 'JWT Authorization Failed !!!';
                } 

            }
            else
            {
                http_response_code(200);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }

        }


        $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
    }
/* Global Search */


?>