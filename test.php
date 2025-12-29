/* lead list by dob & anniversary date */
            public function leadListByDobAnni(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'page_no', 'per_page', 'date_type'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId                    = $getTokenValue['data'][1];
                        $expiry                 = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser                = User::where('id', '=', $uId)->first();

                        $page_no                = $requestData['page_no'];
                        $per_page               = $requestData['per_page'];
                        $date_type              = $requestData['date_type'];
                        $search_text            = $requestData['search_text'];

                        if($getUser){
                            $branch_id                      = $getUser->branch_id;
                            $assigned_telecaller_id         = $uId;
                            $limit                          = $per_page; // per page elements
                            if($page_no == 1){
                                $offset         = 0;
                            } else {
                                $offset         = (($limit * $page_no) - $limit); // ((15 * 3) - 15)
                            }

                            $current_date       = date('d-m');
                            if($date_type == 'dob'){
                                $leadNos = DB::table('branch_leads')
                                                                        ->join('master_leads', 'branch_leads.lead_sl_no', '=', 'master_leads.sl_no')
                                                                        ->select('branch_leads.lead_sl_no', 'branch_leads.parent_status_id', 'branch_leads.child_status_id', 'branch_leads.next_followup_date', 'branch_leads.next_followup_time', 'branch_leads.created_at', 'branch_leads.campaign_type_id', 'branch_leads.campaign_id')
                                                                        ->where('master_leads.header_id', '=', 12)
                                                                        ->where('master_leads.header_value', 'LIKE', '%' . $current_date . '%')
                                                                        ->where('branch_leads.assigned_telecaller_id', '=', $assigned_telecaller_id)
                                                                        ->orderBy('branch_leads.lead_sl_no', 'ASC')
                                                                        ->offset($offset)
                                                                        ->limit($limit)
                                                                        ->get();
                            }
                            if($date_type == 'anni'){
                                $leadNos = DB::table('branch_leads')
                                                                        ->join('master_leads', 'branch_leads.lead_sl_no', '=', 'master_leads.sl_no')
                                                                        ->select('branch_leads.lead_sl_no', 'branch_leads.parent_status_id', 'branch_leads.child_status_id', 'branch_leads.next_followup_date', 'branch_leads.next_followup_time', 'branch_leads.created_at', 'branch_leads.campaign_type_id', 'branch_leads.campaign_id')
                                                                        ->where('master_leads.header_id', '=', 13)
                                                                        ->where('master_leads.header_value', 'LIKE', '%' . $current_date . '%')
                                                                        ->where('branch_leads.assigned_telecaller_id', '=', $assigned_telecaller_id)
                                                                        ->orderBy('branch_leads.lead_sl_no', 'ASC')
                                                                        ->offset($offset)
                                                                        ->limit($limit)
                                                                        ->get();
                            }

                            if($leadNos){
                                foreach($leadNos as $leadNo){
                                    $isShow         = 1;

                                    $activity_count = LeadActivity::where('lead_sl_no', '=', $leadNo->lead_sl_no)->count();
                                    $last_activity  = LeadActivity::where('lead_sl_no', '=', $leadNo->lead_sl_no)->orderBy('id', 'DESC')->first();
                                    $next_schedule  = '';
                                    if($activity_count > 0){
                                        if($leadNo->next_followup_date != '' && $leadNo->next_followup_time != ''){
                                            $next_schedule = date_format(date_create($leadNo->next_followup_date), "M d Y") . ', ' . date_format(date_create($leadNo->next_followup_time), "h:i a");
                                        }
                                    }
                                    $getParentStatus    = LeadStatus::select('name')->where('id', '=', $leadNo->parent_status_id)->first();
                                    $getChildStatus     = LeadStatus::select('name')->where('id', '=', $leadNo->child_status_id)->first();
                                    $getMasterLead      = MasterLead::select('lead_no')->where('sl_no', '=', $leadNo->lead_sl_no)->first();
                                    $getCampaignType    = CampaignType::select('name')->where('id', '=', $leadNo->campaign_type_id)->first();
                                    $getCampaign        = Campaign::select('name')->where('id', '=', $leadNo->campaign_id)->first();

                                    $dob_anni_curr_date = date('d-m');
                                    /* birthday check */
                                        $getBirthday = $this->getHeaderValueByID($leadNo->lead_sl_no, 12); // 15-08-2000
                                        $formattedDOB = substr($getBirthday, 0, 5);  // Output: 15-08
                                        $is_birthday = ($formattedDOB == $dob_anni_curr_date) ? 1 : 0;
                                    /* birthday check */
                                    /* anniversary check */
                                        $getAnniversary = $this->getHeaderValueByID($leadNo->lead_sl_no, 13); // 15-08-2000
                                        $formattedANNI = substr($getAnniversary, 0, 5);  // Output: 15-08
                                        $is_anniversary = ($formattedANNI == $dob_anni_curr_date) ? 1 : 0;
                                    /* anniversary check */

                                    $getDataSearch  = MasterLead::where('status', '=', 1)->where('sl_no', '=', $leadNo->lead_sl_no)->where('header_value', 'LIKE', '%' . $search_text. '%')->count();
                                    if($getDataSearch > 0){
                                        $isShow         = 1;
                                    } else {
                                        $isShow         = 0;
                                    }

                                    if($isShow){
                                        $apiResponse[]      = [
                                            'sl_no'                 => $leadNo->lead_sl_no,
                                            'lead_no'               => (($getMasterLead)?$getMasterLead->lead_no:''),
                                            'company_name'          => $this->getHeaderValueByID($leadNo->lead_sl_no, 1),
                                            'contact_person_name'   => $this->getHeaderValueByID($leadNo->lead_sl_no, 2),
                                            'email'                 => $this->getHeaderValueByID($leadNo->lead_sl_no, 5),
                                            'phone_no'              => '+' . $this->getHeaderValueByID($leadNo->lead_sl_no, 3) . $this->getHeaderValueByID($leadNo->lead_sl_no, 4),
                                            'whatsapp_no'           => '+' . $this->getHeaderValueByID($leadNo->lead_sl_no, 3) . $this->getHeaderValueByID($leadNo->lead_sl_no, 14),
                                            'is_vip'                => (int) $this->getHeaderValueByID($leadNo->lead_sl_no, 17),
                                            'is_purchased'          => (int) $this->getHeaderValueByID($leadNo->lead_sl_no, 18),
                                            'is_birthday'           => (int) $is_birthday,
                                            'is_anniversary'        => (int) $is_anniversary,
                                            'parent_status_id'      => (($leadNo->parent_status_id > 0)?$leadNo->parent_status_id:12),
                                            'parent_status_name'    => (($getParentStatus)?$getParentStatus->name:'New'),
                                            'child_status_id'       => (($leadNo->child_status_id > 0)?$leadNo->child_status_id:13),
                                            'child_status_name'     => (($getChildStatus)?$getChildStatus->name:'New'),
                                            'campaign_type_name'    => (($getCampaignType)?$getCampaignType->name:''),
                                            'campaign_name'         => (($getCampaign)?$getCampaign->name:''),
                                            'last_call'             => (($activity_count > 0)?date_format(date_create($last_activity->created_at), "M d Y, h:i a"):''),
                                            'next_schedule'         => $next_schedule,
                                            'activity_count'        => $activity_count,
                                            'telecaller_name'       => $getUser->first_name . ' ' . $getUser->last_name,
                                        ];
                                    }
                                }
                            }

                            $apiStatus          = TRUE;
                            http_response_code(200);
                            $apiMessage         = 'Data Available !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* lead list by dob & anniversary date */