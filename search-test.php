<?php

if(!empty($request->input('branch')) && !empty($request->input('telecaller')) && !empty($request->input('parent-status')) && !empty($request->input('child-status')) )
{
    $selected_parent_status_id = Helper::decoded($request->input('parent-status'));
    $data['parentWiseChildStatus'] = LeadStatus::where('parent_id', '=', $selected_parent_status_id)->where('status', '!=', 3)->orderBy('rank', 'asc')->get();
    $data['selected_parent_status_id'] = $selected_parent_status_id;

    $selected_child_status_id = Helper::decoded($request->input('child-status'));
    $data['selected_child_status_id'] = $selected_child_status_id;

    $selected_branch_id = Helper::decoded($request->input('branch'));
    $data['branchWiseTelecaller'] = User::where('branch_id', '=', $selected_branch_id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
    $data['selected_branch_id'] = $selected_branch_id;

    $selected_telecaller_id = Helper::decoded($request->input('telecaller'));                              
    $data['selected_telecaller_id'] = $selected_telecaller_id ;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
       
        if($selected_parent_status_id == 12 && $selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id) && !empty($selected_child_status_id), function ($query) use ($selected_parent_status_id, $selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id, $selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->when(!empty($selected_parent_status_id), function ($q) use ($selected_parent_status_id) {
                            $q->where('parent_status_id', $selected_parent_status_id);
                        })
                        ->when(!empty($selected_child_status_id), function ($q) use ($selected_child_status_id) {
                            $q->where('child_status_id', $selected_child_status_id);
                        })
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        }
    
    }
    else
    {
        if($selected_parent_status_id == 12 && $selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('branch_id', '=', $selected_branch_id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('branch_id', '=', $selected_branch_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id) && !empty($selected_child_status_id), function ($query) use ($selected_parent_status_id, $selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id, $selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->when(!empty($selected_parent_status_id), function ($q) use ($selected_parent_status_id) {
                            $q->where('parent_status_id', $selected_parent_status_id);
                        })
                        ->when(!empty($selected_child_status_id), function ($q) use ($selected_child_status_id) {
                            $q->where('child_status_id', $selected_child_status_id);
                        })
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        }
    
    
    }

}
elseif(!empty($request->input('branch')) && !empty($request->input('telecaller')) && !empty($request->input('child-status')))
{
    $selected_branch_id = Helper::decoded($request->input('branch'));
    $data['branchWiseTelecaller'] = User::where('branch_id', '=', $selected_branch_id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
    $data['selected_branch_id'] = $selected_branch_id;

    $selected_telecaller_id = Helper::decoded($request->input('telecaller'));                              
    $data['selected_telecaller_id'] = $selected_telecaller_id ;

    $selected_child_status_id = Helper::decoded($request->input('child-status'));
    $data['selected_child_status_id'] = $selected_child_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        
        if($selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_child_status_id), function ($query) use ($selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('child_status_id', $selected_child_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }

    }
    else
    {
        
        if($selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('branch_id', '=', $selected_branch_id)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('branch_id', '=', $selected_branch_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_child_status_id), function ($query) use ($selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('child_status_id', $selected_child_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    

    }

}
elseif(!empty($request->input('branch')) && !empty($request->input('telecaller')) && !empty($request->input('parent-status')))
{
    $selected_branch_id = Helper::decoded($request->input('branch'));
    $data['branchWiseTelecaller'] = User::where('branch_id', '=', $selected_branch_id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
    $data['selected_branch_id'] = $selected_branch_id;

    $selected_telecaller_id = Helper::decoded($request->input('telecaller'));                              
    $data['selected_telecaller_id'] = $selected_telecaller_id ;

    $selected_parent_status_id = Helper::decoded($request->input('parent-status'));

    $data['parentWiseChildStatus'] = LeadStatus::where('parent_id', '=', $selected_parent_status_id)->where('status', '!=', 3)->orderBy('rank', 'asc')->get();

    $data['selected_parent_status_id'] = $selected_parent_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
       
        if($selected_parent_status_id == 12)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('parent_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id), function ($query) use ($selected_parent_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('parent_status_id', $selected_parent_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }


    }
    else
    {
        
        if($selected_parent_status_id == 12)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('branch_id', '=', $selected_branch_id)->where('parent_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('branch_id', '=', $selected_branch_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id), function ($query) use ($selected_parent_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('parent_status_id', $selected_parent_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    }

}
elseif(!empty($request->input('telecaller')) && !empty($request->input('parent-status')) && !empty($request->input('child-status')))
{
    $selected_parent_status_id = Helper::decoded($request->input('parent-status'));
    $data['parentWiseChildStatus'] = LeadStatus::where('parent_id', '=', $selected_parent_status_id)->where('status', '!=', 3)->orderBy('rank', 'asc')->get();
    $data['selected_parent_status_id'] = $selected_parent_status_id;

    $selected_child_status_id = Helper::decoded($request->input('child-status'));
    $data['selected_child_status_id'] = $selected_child_status_id;

    $selected_telecaller_id = Helper::decoded($request->input('telecaller'));                              
    $data['selected_telecaller_id'] = $selected_telecaller_id ;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;                

        if($selected_parent_status_id == 12 && $selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id) && !empty($selected_child_status_id), function ($query) use ($selected_parent_status_id, $selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id, $selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->when(!empty($selected_parent_status_id), function ($q) use ($selected_parent_status_id) {
                            $q->where('parent_status_id', $selected_parent_status_id);
                        })
                        ->when(!empty($selected_child_status_id), function ($q) use ($selected_child_status_id) {
                            $q->where('child_status_id', $selected_child_status_id);
                        })
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        }
    
    }
    else
    {

        if($selected_parent_status_id == 12 && $selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id) && !empty($selected_child_status_id), function ($query) use ($selected_parent_status_id, $selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id, $selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->when(!empty($selected_parent_status_id), function ($q) use ($selected_parent_status_id) {
                            $q->where('parent_status_id', $selected_parent_status_id);
                        })
                        ->when(!empty($selected_child_status_id), function ($q) use ($selected_child_status_id) {
                            $q->where('child_status_id', $selected_child_status_id);
                        })
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        }
    
    
    }

}
elseif(!empty($request->input('branch')) && !empty($request->input('parent-status')) && !empty($request->input('child-status')))
{
    $selected_parent_status_id = Helper::decoded($request->input('parent-status'));
    $data['parentWiseChildStatus'] = LeadStatus::where('parent_id', '=', $selected_parent_status_id)->where('status', '!=', 3)->orderBy('rank', 'asc')->get();
    $data['selected_parent_status_id'] = $selected_parent_status_id;

    $selected_child_status_id = Helper::decoded($request->input('child-status'));
    $data['selected_child_status_id'] = $selected_child_status_id;

    $selected_branch_id = Helper::decoded($request->input('branch'));
    $data['branchWiseTelecaller'] = User::where('branch_id', '=', $selected_branch_id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
    $data['selected_branch_id'] = $selected_branch_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;

        if($selected_parent_status_id == 12 && $selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $this_telecaller_id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id) && !empty($selected_child_status_id), function ($query) use ($selected_parent_status_id, $selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id, $selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->when(!empty($selected_parent_status_id), function ($q) use ($selected_parent_status_id) {
                            $q->where('parent_status_id', $selected_parent_status_id);
                        })
                        ->when(!empty($selected_child_status_id), function ($q) use ($selected_child_status_id) {
                            $q->where('child_status_id', $selected_child_status_id);
                        })
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        }
    
    }
    else
    {
       
        if($selected_parent_status_id == 12 && $selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id) && !empty($selected_child_status_id), function ($query) use ($selected_parent_status_id, $selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id, $selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->when(!empty($selected_parent_status_id), function ($q) use ($selected_parent_status_id) {
                            $q->where('parent_status_id', $selected_parent_status_id);
                        })
                        ->when(!empty($selected_child_status_id), function ($q) use ($selected_child_status_id) {
                            $q->where('child_status_id', $selected_child_status_id);
                        })
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        }
    
    
    }

}
elseif(!empty($request->input('telecaller')) && !empty($request->input('child-status')))
{
    $selected_telecaller_id = Helper::decoded($request->input('telecaller'));                              
    $data['selected_telecaller_id'] = $selected_telecaller_id ;

    $selected_child_status_id = Helper::decoded($request->input('child-status'));
    $data['selected_child_status_id'] = $selected_child_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;

        if($selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_child_status_id), function ($query) use ($selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('child_status_id', $selected_child_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }

    }
    else
    {

        if($selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_child_status_id), function ($query) use ($selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('child_status_id', $selected_child_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    
    }
}
elseif(!empty($request->input('telecaller')) && !empty($request->input('parent-status')))
{
    $selected_telecaller_id = Helper::decoded($request->input('telecaller'));                              
    $data['selected_telecaller_id'] = $selected_telecaller_id ;

    $selected_parent_status_id = Helper::decoded($request->input('parent-status'));
    $data['parentWiseChildStatus'] = LeadStatus::where('parent_id', '=', $selected_parent_status_id)->where('status', '!=', 3)->orderBy('rank', 'asc')->get();
    $data['selected_parent_status_id'] = $selected_parent_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;

        if($selected_parent_status_id == 12)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('parent_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id), function ($query) use ($selected_parent_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('parent_status_id', $selected_parent_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }

    }
    else
    {

        if($selected_parent_status_id == 12)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('parent_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id), function ($query) use ($selected_parent_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('parent_status_id', $selected_parent_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    
    }
}
elseif(!empty($request->input('branch')) && !empty($request->input('child-status')))
{
    $selected_branch_id = Helper::decoded($request->input('branch'));
    $data['branchWiseTelecaller'] = User::where('branch_id', '=', $selected_branch_id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
    $data['selected_branch_id'] = $selected_branch_id;

    $selected_child_status_id = Helper::decoded($request->input('child-status'));
    $data['selected_child_status_id'] = $selected_child_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        
        if($selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $this_telecaller_id)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_child_status_id), function ($query) use ($selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('child_status_id', $selected_child_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
        
    }
    else
    {

        if($selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_child_status_id), function ($query) use ($selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('child_status_id', $selected_child_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    
    }
}
elseif(!empty($request->input('branch')) && !empty($request->input('parent-status')))
{           
    $selected_branch_id = Helper::decoded($request->input('branch'));
    $data['branchWiseTelecaller'] = User::where('branch_id', '=', $selected_branch_id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
    $data['selected_branch_id'] = $selected_branch_id;

    $selected_parent_status_id = Helper::decoded($request->input('parent-status'));   
    $data['parentWiseChildStatus'] = LeadStatus::where('parent_id', '=', $selected_parent_status_id)->where('status', '!=', 3)->orderBy('rank', 'asc')->get();
    $data['selected_parent_status_id'] = $selected_parent_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        
        if($selected_parent_status_id == 12)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $this_telecaller_id)->where('parent_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id), function ($query) use ($selected_parent_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('parent_status_id', $selected_parent_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }


    }
    else
    {                
        
        if($selected_parent_status_id == 12)
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('parent_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id), function ($query) use ($selected_parent_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('parent_status_id', $selected_parent_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    

    }

}
elseif(!empty($request->input('branch')) && !empty($request->input('telecaller')))
{
    $selected_branch_id = Helper::decoded($request->input('branch'));
    $data['branchWiseTelecaller'] = User::where('branch_id', '=', $selected_branch_id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
    $data['selected_branch_id'] = $selected_branch_id;

    $selected_telecaller_id = Helper::decoded($request->input('telecaller'));                              
    $data['selected_telecaller_id'] = $selected_telecaller_id ;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }
    else
    {
        $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('branch_id', '=', $selected_branch_id)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

}
elseif(!empty($request->input('telecaller')))
{
    $selected_telecaller_id = Helper::decoded($request->input('telecaller'));                              
    $data['selected_telecaller_id'] = $selected_telecaller_id ;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }
    else
    {
        $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $selected_telecaller_id)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

}
elseif(!empty($request->input('branch')))
{
    $selected_branch_id = Helper::decoded($request->input('branch'));
    // dd($selected_branch_id);
    $data['branchWiseTelecaller'] = User::where('branch_id', '=', $selected_branch_id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();

    $data['selected_branch_id'] = $selected_branch_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }
    else
    {
        $branchleadPaginated = BranchLead::where('branch_id', '=', $selected_branch_id)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

}
elseif(!empty($request->input('parent-status')) && !empty($request->input('child-status')))
{
    $selected_parent_status_id = Helper::decoded($request->input('parent-status'));
    $data['parentWiseChildStatus'] = LeadStatus::where('parent_id', '=', $selected_parent_status_id)->where('status', '!=', 3)->orderBy('rank', 'asc')->get();
    $data['selected_parent_status_id'] = $selected_parent_status_id;

    $selected_child_status_id = Helper::decoded($request->input('child-status'));
    $data['selected_child_status_id'] = $selected_child_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        // where('assigned_telecaller_id', '=', $this_telecaller_id)

        if($selected_parent_status_id == 12 && $selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $this_telecaller_id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id) && !empty($selected_child_status_id), function ($query) use ($selected_parent_status_id, $selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id, $selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->when(!empty($selected_parent_status_id), function ($q) use ($selected_parent_status_id) {
                            $q->where('parent_status_id', $selected_parent_status_id);
                        })
                        ->when(!empty($selected_child_status_id), function ($q) use ($selected_child_status_id) {
                            $q->where('child_status_id', $selected_child_status_id);
                        })
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        }
    
    }
    else
    {
        if($selected_parent_status_id == 12 && $selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id) && !empty($selected_child_status_id), function ($query) use ($selected_parent_status_id, $selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id, $selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->when(!empty($selected_parent_status_id), function ($q) use ($selected_parent_status_id) {
                            $q->where('parent_status_id', $selected_parent_status_id);
                        })
                        ->when(!empty($selected_child_status_id), function ($q) use ($selected_child_status_id) {
                            $q->where('child_status_id', $selected_child_status_id);
                        })
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        }
    
    
    }

}
elseif(!empty($request->input('child-status')))
{
    $selected_child_status_id = Helper::decoded($request->input('child-status'));

    $data['selected_child_status_id'] = $selected_child_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        // where('assigned_telecaller_id', '=', $this_telecaller_id)

        if($selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $this_telecaller_id)->where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_child_status_id), function ($query) use ($selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('child_status_id', $selected_child_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    }
    else
    {
        if($selected_child_status_id == 13)
        {
            $branchleadPaginated = BranchLead::where('child_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_child_status_id), function ($query) use ($selected_child_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_child_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('child_status_id', $selected_child_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    
    
    }
}
elseif(!empty($request->input('parent-status')))
{
    $selected_parent_status_id = Helper::decoded($request->input('parent-status'));

    $data['parentWiseChildStatus'] = LeadStatus::where('parent_id', '=', $selected_parent_status_id)->where('status', '!=', 3)->orderBy('rank', 'asc')->get();

    $data['selected_parent_status_id'] = $selected_parent_status_id;

    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;

        if($selected_parent_status_id == 12)
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $this_telecaller_id)->where('parent_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id), function ($query) use ($selected_parent_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('parent_status_id', $selected_parent_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }


    }
    else
    {
        if($selected_parent_status_id == 12)
        {
            $branchleadPaginated = BranchLead::where('parent_status_id', '=', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('status', '!=', 3)
            ->whereHas('leadActivities', function ($query) {
                $query->where('status', '!=', 3);
            })
            ->when(!empty($selected_parent_status_id), function ($query) use ($selected_parent_status_id) {
                $query->whereIn('lead_sl_no', function ($subQuery) use ($selected_parent_status_id) {
                    $subQuery->select('lead_sl_no')
                        ->from('lead_activities as la')
                        ->where('status', '!=', 3)
                        ->where('parent_status_id', $selected_parent_status_id)
                        ->whereRaw('la.id = (
                            SELECT MAX(id) FROM lead_activities WHERE lead_sl_no = la.lead_sl_no
                        )'); // only latest
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        }
    
    
    }
}
else // no filter
{
    if(session('user_data')['role_id'] == 3)
    {
        $this_telecaller_id = session('user_data')['user_id'] ;
        $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }
    else
    {
        $branchleadPaginated = BranchLead::where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }
}

?>