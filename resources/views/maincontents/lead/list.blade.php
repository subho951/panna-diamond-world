<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')

    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <h4><?= $page_header ?></h4>
            <h6 class="breadcrumb-wrapper">
                <span class="text-muted fw-light"><a href="<?= url('dashboard') ?>">Dashboard</a> /</span> <?= $page_header ?>
            </h6>
            <div class="nav-align-top mb-4">
                <?php if(session('success_message')){?>
                <div class="alert alert-success alert-dismissible autohide" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-desktop align-top me-2"></i>Success!</h6>
                    <span><?= session('success_message') ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
                <?php }?>
                <?php if(session('error_message')){?>
                <div class="alert alert-danger alert-dismissible autohide" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-store align-top me-2"></i>Error!</h6>
                    <span><?= session('error_message') ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
                <?php }?>
                <div class="card mb-4">
                    {{-- <div class="card-header">
                        <a href="<?= url($controllerRoute . '/add/') ?>"
                            class="btn btn-outline-success btn-sm float-end">Add <?= $module['title'] ?></a>
                    </div> --}}
                    <div class="card-body">
                        {{-- <div id="table-overlay-loader" class="text-loader">
                  Fetching data. Please wait <span id="dot-animation">.</span>
               </div> --}}
                        <h6 class="card-title">Filter</h6>
                        <form class="mb-3">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <select id="filter_id" class="select2 form-select" data-allow-clear="true" required>

                                        <option value="" disable selected>Select Status</option>
                                        <option value="PHONE NOT CONNECTED">PHONE NOT CONNECTED</option>
                                        <option value="DID NOT PICKUP">DID NOT PICKUP</option>
                                        <option value="CALL LATER">CALL LATER</option>
                                        <option value="FOLLOW UP">FOLLOW UP</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select id="" class="select2 form-select" data-allow-clear="true">
                                        <option value="" disable selected>ALL</option>

                                    </select>
                                </div>
                                <div class="col-4 col-md-2 mb-2">
                                    <button type="button" class="btn btn-outline-dark">
                                        <i class="fa-solid fa-filter"></i>&nbsp;<span>Filter Leads</span>
                                    </button>
                                </div>
                                <div class="col-8 col-md-2 mb-2">
                                    <button type="button" class="btn btn-label-secondary">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>&nbsp;<span>Reset</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <h6 class="card-title">Transfer Lead To</h6>
                        <form class="mb-5">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <select id="" class="select2 form-select" data-allow-clear="true" required>
                                        <option value="" disable selected>Select User</option>

                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button type="button" class="btn btn-outline-dark rounded-pill">
                                        <i class="fas fa-exchange-alt"></i>&nbsp;<span>Bulk Lead Transfer</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="card p-3">
                            {{-- <h5 class="card-header fw-bold text-success p-2">Lead List</h5> --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    {{-- <span class="card-header fw-bold text-success h5 ps-0">Lead List</span>  --}}
                                    @if(!empty($totalLeadArr))
                                        <label for="perPageSelect" class="form-label me-2" style="font-size: 12px;">Show</label>
                                        <select id="perPageSelect" class="form-select d-inline-block" style="width: 70px !important;padding: 2px !important;font-size: 12px;">
                                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                            <option value="250" {{ $perPage == 250 ? 'selected' : '' }}>250</option>
                                            <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
                                        </select>
                                        <span style="font-size: 12px;">entries</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap">

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            @if(!empty($totalLeadArr))
                                                <th><input type="checkbox" name="" id=""></th>
                                            @endif
                                            <th>#</th>
                                            <th>Lead No</th>
                                            <th>Details</th>
                                            <th>Last Activity</th>
                                            <th>Next Schedule</th>

                                            @if(session('user_data')['role_id'] != 3)
                                                <th>Assigned User | Branch</th>
                                            @endif
                                            
                                            <th style="text-align: center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">

                                        @if(!empty($totalLeadArr))

                                        @foreach($totalLeadArr as $eachLeadArr)
                                        {{-- @dd($eachLeadArr); --}}
                                        <tr>
                                            <td><input type="checkbox" name="" id=""></td>

                                            <td>{{ ($loop->iteration) + ($perPage * ($page - 1)) }}</td>
                                            <td>{{ $eachLeadArr->lead_no }}</td>

                                            <td>
                                               @foreach($eachLeadArr->eachLeadDetailsArr as $key => $value)
                                               {{-- @dd($key, $value); --}}
                                                    @if( ($value["is_visible_in_lead_list"] == "YES") && !empty($value["contact-person-name"]) )
                                                        <span class="badge badge-center rounded-pill bg-label-info mb-1">
                                                            <i class="fa-solid fa-user"></i>
                                                        </span> <span class="fw-bold text-primary">{{ $value["contact-person-name"] }}</span>
                                                        <br>
                                                    @endif
                                                    @if( ($value["is_visible_in_lead_list"] == "YES") && !empty($value["phone"]))
                                                        <span class="badge badge-center rounded-pill bg-label-info mb-1">
                                                            <i class="fa-solid fa-phone"></i>
                                                        </span> <span class="fw-bold text-primary">{{ $value["phone"] }}</span>
                                                        <br>
                                                    @endif
                                                    @if( ($value["is_visible_in_lead_list"] == "YES") && !empty($value["whatsapp-number"]))
                                                        <span class="badge badge-center rounded-pill bg-label-info mb-1">
                                                            <i class="fa-brands fa-whatsapp"></i>
                                                        </span> <span class="fw-bold text-primary">{{ $value["whatsapp-number"] }}</span>
                                                        <br>
                                                    @endif
                                                    @if( ($value["is_visible_in_lead_list"] == "YES") && !empty($value["email"]))
                                                        <span class="badge badge-center rounded-pill bg-label-info mb-1">
                                                            <i class="fa-solid fa-envelope"></i>
                                                        </span> <span class="fw-bold text-primary">{{ $value["email"] }}</span>
                                                        <br>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @if(!empty($eachLeadArr->lead_created_at))
                                                    <span>{{ $eachLeadArr->lead_created_at }}</span>
                                                    <br>
                                                @endif
                                                {{-- <span class="badge bg-label-info me-1 mt-1"></span> --}}
                                                @if(!empty($eachLeadArr->parentStatus))
                                                    @foreach ($eachLeadArr->parentStatus as $key => $value)
                                                        @php
                                                            if($key == 'name') {
                                                                $name = $value;
                                                            }
                                                            if($key == 'background_color') {
                                                                $background = $value;
                                                            }
                                                            if($key == 'font_color'){
                                                                $color = $value;
                                                            }
                                                        @endphp
                                                    @endforeach
                                                    <span class="badge me-1 mt-1" style="background-color: {{ $background }}; color: {{ $color }};">{{ $name }}</span>
                                                    {{-- <span class="badge bg-label-info me-1 mt-1">{{ $name }}</span> --}}
                                                    <br>
                                                @endif

                                                @if(!empty($eachLeadArr->childStatus))
                                                    @foreach ($eachLeadArr->childStatus as $key => $value)
                                                        @php
                                                            if($key == 'name') {
                                                                $name = $value;
                                                            }
                                                            if($key == 'background_color') {
                                                                $background = $value;
                                                            }
                                                            if($key == 'font_color'){
                                                                $color = $value;
                                                            }
                                                        @endphp
                                                    @endforeach
                                                    <span class="badge me-1 mt-1" style="background-color: {{ $background }}; color: {{ $color }};">{{ $name }}</span>
                                                    {{-- <span class="badge bg-label-info me-1 mt-1">{{ $name }}</span> --}}
                                                    <br>
                                                @endif
                                                
                                                <span class="badge rounded-pill bg-label-success mt-1">{{ $eachLeadArr->lead_activity_count }}</span>
                                            </td>
                                            <td>
                                                @if(!empty($eachLeadArr->scheduled_date_time))
                                                <span class="badge badge-center rounded-pill bg-label-warning">
                                                    <i class="fa-regular fa-clock"></i>
                                                 </span>  <span>{{ $eachLeadArr->scheduled_date_time }}</span>{{--<span>Mar 03, 2025 03:23 PM</span> --}}
                                                 @endif
                                            </td>

                                            @if(session('user_data')['role_id'] != 3)
                                            <td>
                                                @if(!empty($eachLeadArr->assigned_telecaller_name))
                                                    <span class="badge badge-center rounded-pill bg-label-danger mt-1">
                                                        <i class="fa-solid fa-user-tie"></i>
                                                    </span> <span>{{ $eachLeadArr->assigned_telecaller_name }}</span>
                                                    <br>
                                                @endif
                                                @if(!empty($eachLeadArr->branch_name))
                                                    <span class="badge badge-center rounded-pill bg-label-secondary mt-1">
                                                        <i class="fas fa-sitemap"></i>
                                                    </span> <span>{{ $eachLeadArr->branch_name }}</span>
                                                @endif
                                            </td>
                                            @endif
                                            
                                            {{-- Actions:   w.r.t. BranchLead ID --}}
                                            <td style="text-align: center">
                                               <button class="callButton btn btn-sm btn-outline-dark mb-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#callModal"
                                                        title="Call"
                                                        data-id="{{ Helper::encoded($eachLeadArr->id) }}">
                                                    <i class="fa-solid fa-headset"></i>&nbsp;<span>Call</span>
                                                </button>
                                                
                                                @if(session('user_data')['role_id'] != 3)
                                                    <br>
                                                    <a href="{{url($controllerRoute.'/edit/'. Helper::encoded($eachLeadArr->id))}}" class="btn btn-sm btn-primary mb-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a target="_blank" href="{{url($controllerRoute.'/view-lead/'. Helper::encoded($eachLeadArr->id))}}" class="btn btn-sm btn-info mb-1" title="View">
                                                        <i class="fas fa-info-circle"></i>
                                                    </a>
                                                    <br>

                                                    @if ($eachLeadArr->status == 1)
                                                        <a href="{{url($controllerRoute.'/change-status/'. Helper::encoded($eachLeadArr->id))}}" class="btn btn-sm btn-success me-1 mb-1" onclick="return confirm('Do you want to deactivate this lead ?')" title="Deactivate">
                                                            <i class="fa-solid fa-check"></i>
                                                        </a>
                                                    @else 
                                                        <a href="{{url($controllerRoute.'/change-status/'. Helper::encoded($eachLeadArr->id))}}" class="btn btn-sm btn-warning me-1 mb-1" onclick="return confirm('Do you want to activate this lead ?')" title="Activate">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif

                                                    <a href="{{url($controllerRoute.'/delete/'. Helper::encoded($eachLeadArr->id))}}" class="btn btn-sm btn-danger mb-1"
                                                        onclick="return confirm('Are you sure ?')" title="Delete">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                    {{-- <a href="" class="btn btn-sm btn-outline-dark mb-1"
                                                        title="Transfer Lead To Another User">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a> --}}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach

                                        @else
                                            <tr>
                                                <td style="color:red; text-align:center;" colspan="7">No records available</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                                
                                
                            </div>


                            {{-- Pagination --}}
                            @if(!empty($totalLeadArr))
                                @php
                                    $currentPage = $branchleadPaginated->currentPage();
                                    $lastPage = $branchleadPaginated->lastPage();
                                
                                    // show 3 pages around current
                                    $start = max(1, $currentPage - 1);
                                    $end = min($lastPage, $start + 2);
                                
                                    // adjust if we’re at the last pages
                                    if (($end - $start) < 2) {
                                        $start = max(1, $end - 2);
                                    }
                                @endphp
                        
                                <nav class="mt-2">
                                    <ul class="pagination pagination-sm">
                                        
                                        {{-- First --}}
                                        <li class="page-item first {{ $currentPage == 1 ? 'disabled' : '' }}" 
                                            title="First" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="tooltip-primary">
                                            <a class="page-link" href="{{ $currentPage == 1 ? 'javascript:void(0);' : $branchleadPaginated->url(1) }}">
                                                <i class="fa fa-angle-double-left fa-xs"></i>
                                            </a>
                                        </li>
                                
                                        {{-- Prev --}}
                                        <li class="page-item prev {{ $currentPage == 1 ? 'disabled' : '' }}" 
                                            title="Prev" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="tooltip-primary">
                                            <a class="page-link" href="{{ $currentPage == 1 ? 'javascript:void(0);' : $branchleadPaginated->previousPageUrl() }}">
                                                <i class="fa-solid fa-chevron-left fa-xs"></i>
                                            </a>
                                        </li>
                                
                                        {{-- Page Numbers (max 3) --}}
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $branchleadPaginated->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor
                                
                                        {{-- Next --}}
                                        <li class="page-item next {{ $currentPage == $lastPage ? 'disabled' : '' }}" 
                                            title="Next" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="tooltip-primary">
                                            <a class="page-link" href="{{ $currentPage == $lastPage ? 'javascript:void(0);' : $branchleadPaginated->nextPageUrl() }}">
                                                <i class="fa-solid fa-chevron-right fa-xs"></i>
                                            </a>
                                        </li>
                                
                                        {{-- Last --}}
                                        <li class="page-item last {{ $currentPage == $lastPage ? 'disabled' : '' }}" 
                                            title="Last" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="tooltip-primary">
                                            <a class="page-link" href="{{ $currentPage == $lastPage ? 'javascript:void(0);' : $branchleadPaginated->url($lastPage) }}">
                                                <i class="fa fa-angle-double-right fa-xs"></i>
                                            </a>
                                        </li>
                                
                                    </ul>
                                </nav>                        
                            @endif

                                                      
                        </div>

                        <!-- call modal -->
                        <div class="modal fade" id="callModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-simple modal-edit-user">
                                <div class="modal-content">
                                    <div class="modal-body p-0">
                                        <!-- modal body -->
                                        
                                          
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- call modal -->
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleEdit(showEdit) {
            document.getElementById('leadDisplay').classList.toggle('d-none', showEdit);
            document.getElementById('leadEdit').classList.toggle('d-none', !showEdit);
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
    <script>
    $(document).ready(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        //Pagination
        document.getElementById('perPageSelect').addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('perPage', this.value);
            url.searchParams.set('page', 1); // reset to first page
            window.location.href = url.toString();
        });
    
        let baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
        const base_url = document.querySelector('meta[name="baseurl"]').getAttribute('content');

        let modalClosed = false;
        let leadStatusUpdated = false;

        let id = ""; // BranchLead ID, global scope

        $(document).on('click', '.callButton', function() {
            id = $(this).data('id'); // BranchLead ID

            $.ajax({
                url: base_url + '/lead-list/get-lead-call-data',
                type: 'POST',
                data: {
                    branchLead_id: id
                },
                success: function(response) {
                    $('#callModal .modal-body').html(response.html);
                    $('#callModal').modal('show'); // force show

                    // console.log(response);
                },
                error: function(xhr) {
                    $('#callModal .modal-body').html('');
                    $('#callModal').modal('hide'); // force hide
                    alert('Error loading lead data.');
                    console.log(xhr);
                }
            });
        });

        //disable next follow up date and time if lead status is dump
        $(document).on("change", "#leadStatus", function () 
        {
            let selectedStatus = $("#leadStatus option:selected");
            let statusVal = selectedStatus.val() ?? "";

            // Select related fields and labels
            let $followUpDate = $("#nextFollowUpDate");
            let $followUpTime = $("#nextFollowUpTime");

            let $dateLabel = $("label[for='nextFollowUpDate']");
            let $timeLabel = $("label[for='nextFollowUpTime']");

            // Define the red star span selector
            let redStar = "<span class=\"text-danger\">*</span>";

            if (statusVal.toLowerCase().includes("[dump]")) {
                // Disable inputs
                $followUpDate.prop("disabled", true).removeAttr("required").val("");
                $followUpTime.prop("disabled", true).removeAttr("required").val("");

                // Remove star spans from labels
                $dateLabel.find("span.text-danger").remove();
                $timeLabel.find("span.text-danger").remove();

            } else {
                // Re-enable inputs
                $followUpDate.prop("disabled", false).attr("required", true);
                $followUpTime.prop("disabled", false).attr("required", true);

                // Re-add star spans if not present
                if ($dateLabel.find("span.text-danger").length === 0) {
                    $dateLabel.append(redStar);
                }
                if ($timeLabel.find("span.text-danger").length === 0) {
                    $timeLabel.append(redStar);
                }
            }

        });



        // fetch lead history from lead activity
        function fetchLeadHistory(id) //BranchLead ID
        {
            $.ajax({
                url: base_url + '/lead-list/fetch-lead-history',
                type: 'POST',
                data: {branchLead_id : id},
                success: function(leadHistoryArr)
                {
                    // console.log(leadHistoryArr);
                    if (leadHistoryArr.length != 0)
                    {
                        $('.leadHistoryContainer').empty();

                        leadHistoryArr.forEach(leadHistoryRow => 
                        {   
                            // handling lead status 
                            let statusBadgesHTML = '';

                            if(leadHistoryRow.parentStatus?.background_color && leadHistoryRow.parentStatus?.font_color && leadHistoryRow.parentStatus?.name) 
                            {
                                statusBadgesHTML += `
                                    <span class="badge bg-glow me-1 mt-1 ms-1" style="background-color: ${leadHistoryRow.parentStatus.background_color}; color: ${leadHistoryRow.parentStatus.font_color}; font-size: 11px;">
                                        ${leadHistoryRow.parentStatus.name}
                                    </span>`;
                            }

                            if(leadHistoryRow.childStatus?.background_color && leadHistoryRow.childStatus?.font_color && leadHistoryRow.childStatus?.name) 
                            {
                                statusBadgesHTML += `
                                    <span class="badge bg-glow me-1 mt-1" style="background-color: ${leadHistoryRow.childStatus.background_color}; color: ${leadHistoryRow.childStatus.font_color}; font-size: 11px;">
                                        ${leadHistoryRow.childStatus.name}
                                    </span>`;
                            }

                            let statusBadgeWrapper = statusBadgesHTML ? `<div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                <span class="badge badge-center rounded-pill bg-white text-primary">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>${statusBadgesHTML}</div>` : '';

    
                            $('.leadHistoryContainer').append(`
                                <div class="bg-label-success rounded p-3 mb-3">
                                    <div class="row align-items-center">
                                        
                                        ${leadHistoryRow.last_call ? `
                                            <div class="col">
                                                <div class="d-flex align-items-center small">
                                                    <span class="badge badge-center rounded-pill bg-white text-primary">
                                                        <i class="fa-solid fa-headset"></i>
                                                    </span>
                                                    <span class="text-primary ms-1" style="font-size: 11px;">
                                                        Last Call: <strong>${leadHistoryRow.last_call}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                        ` : ''}

                                        ${leadHistoryRow.assigned_telecaller_name ? `
                                            <div class="col-auto text-end">
                                                <span class="fw-semibold text-primary" style="font-size: 12px;">
                                                    ${leadHistoryRow.assigned_telecaller_name}
                                                </span>
                                            </div>
                                        ` : ''}

                                    </div>

                                    ${leadHistoryRow.purpose_name ? `
                                        <div class="small mt-2">
                                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                                <i class="fa-solid fa-note-sticky"></i>
                                            </span>
                                            <span class="ms-1 text-primary" style="font-size: 11px;">
                                                Purpose: <strong>${leadHistoryRow.purpose_name}</strong>
                                            </span>
                                        </div>
                                    ` : ''}
                                    
                                    ${leadHistoryRow.campaign_type_name && leadHistoryRow.campaign_name ? `
                                        <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                                <i class="fa-solid fa-bullhorn"></i>
                                            </span>
                                            <span class="badge bg-label-dark bg-glow me-1 mt-1 ms-1" style="font-size: 11px;">${leadHistoryRow.campaign_type_name}</span>
                                            <span class="badge bg-label-dark bg-glow me-1 mt-1" style="font-size: 11px;">${leadHistoryRow.campaign_name}</span>
                                        </div>
                                    ` : ''}

                                    ${statusBadgeWrapper}
                        
                                    ${leadHistoryRow.comment ? `
                                        <div class="small mt-2">
                                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                                <i class="fa-solid fa-comment-dots"></i>
                                            </span>
                                            <span class="ms-1" style="font-size: 11px;">
                                                Comment: <strong>${leadHistoryRow.comment} </strong>
                                            </span>
                                        </div>
                                    ` : ''}

                                    ${leadHistoryRow.mood && leadHistoryRow.mood.emoji && leadHistoryRow.mood.name && leadHistoryRow.mood.color ? `
                                        <div class="small mt-2">
                                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                                ${leadHistoryRow.mood.emoji}
                                            </span>
                                            <span class="ms-1 badge rounded-pill bg-white bg-glow" style="color: ${leadHistoryRow.mood.color}; font-size: 11px;">
                                                <strong>${leadHistoryRow.mood.name}</strong>
                                            </span>
                                        </div>
                                    ` : ''}

                                    ${Array.isArray(leadHistoryRow.feedbackTagNameArr) && leadHistoryRow.feedbackTagNameArr.length > 0 ? `
                                        <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                            <span class="badge badge-center rounded-pill bg-white text-primary me-1">
                                                <i class="fa-solid fa-clipboard-list"></i>
                                            </span>
                                            ${leadHistoryRow.feedbackTagNameArr.map(tag => `
                                                <span class="badge bg-glow rounded-pill bg-dark text-white me-1 mt-1" style="font-size: 11px;">${tag}</span>
                                            `).join('')}
                                        </div>
                                    ` : ''}
                                                    
                                    ${leadHistoryRow.next_followup_date || leadHistoryRow.next_followup_time ? `
                                        <div class="text-secondary d-flex align-items-center small mt-1">
                                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                            </span>
                                            <span class="ms-1" style="font-size: 11px;">
                                                Next Schedule: <strong>${leadHistoryRow.next_followup_date} ${leadHistoryRow.next_followup_time}</strong>
                                            </span>
                                        </div>
                                    ` : ''}

                                </div>
                            `);
                        });
                    }
                    else
                    {
                        $('.leadHistoryContainer').empty();
                        $('.leadHistoryContainer').append(`
                            
                            <div class="d-flex justify-content-center align-items-center" style="height: 100%; min-height: 150px;">
                                <p class="fw-semibold text-danger m-0" style="font-size: 12px;">No Activity Found.</p>
                            </div>
                            
                        `);
                    }
                }
                ,
                error: function(err)
                {
                    console.error('Fetch failed:', err);
                }

            });
        }
    
        // fetch lead activity count and sl no.
        function fetchLeadActivityCount(id) //BranchLead ID
        {
            $.ajax({
                url: base_url + '/lead-list/fetch-lead-activity-count',
                type: 'POST',
                data: {branchLead_id : id},
                success: function(res)
                {
                    // console.log(res);
                    $('.leadActivity').empty();
                    $('.leadActivity').append(`
                        Lead Activity(${res.lead_activity_count}) : <span class="badge bg-label-primary">${res.lead_no}</span>
                    `);
                }
                ,
                error: function(err)
                {
                    console.log(err);
                }
            });
        }
        
        // update lead status
        $(document).on('submit', '#updateLeadStatusForm', function(e)
        {
            e.preventDefault();
            formData = new FormData(this);

            let selectedStatus = $("#leadStatus option:selected");
            if(selectedStatus != "")
            {
                formData.append("parent_status_id", selectedStatus.data("parent_status_id"));
                formData.append("child_status_id", selectedStatus.data("child_status_id"));
            }

            formData.append("branchLead_id", id);

            // console.log(formData);

            $.ajax({
                url: base_url + '/lead-list/update-lead-status',
                type: 'POST',
                data: formData,
                processData: false, // prevent jQuery from transforming the data into a query string
                contentType: false, // prevent jQuery from overriding the Content-Type header
                success: function(res)
                {
                    if (res.success_message) 
                    {
                        fetchLeadHistory(id); //BranchLead ID
                        fetchLeadActivityCount(id); //BranchLead ID

                        toastAlert('success', res.success_message);

                        leadStatusUpdated = true;
                    }

                    if (res.error_message) 
                    {
                        toastAlert('error', res.error_message);
                    }


                    $('#updateLeadStatusForm')[0].reset(); // resets basic form inputs

                    // Reset select manually
                    $('#leadStatus').val('').trigger('change'); 
                    $('#nextFollowUpDate').val('');
                    $('#nextFollowUpTime').val('');
                    $('#callPurpose').val('').trigger('change');
                    $('#mood').val('').trigger('change');
                    $('#feedbackTag').val('').trigger('change');
                }
                ,
                error: function(err)
                {
                    console.error('Update failed:', err);
                }
            });
            
        });


        // if modal is closed and status has been successfully updated then refresh the page
        let modalEl = document.getElementById("callModal");
        // When modal is fully hidden
        modalEl.addEventListener("hidden.bs.modal", function () {
            
            modalClosed = true;

            if(modalClosed && leadStatusUpdated)
            {
                // console.log("Modal closed and lead status updated -> Page refreshed");
                location.reload();
            }

            modalClosed = false;
            leadStatusUpdated = false;
            
        });


    });
    </script>
    <script>
        function toastAlert(type, message, redirectStatus = false, redirectUrl = ''){
          toastr.options = {
              "closeButton": true,
              "debug": true,
              "newestOnTop": false,
              "progressBar": true,
              "positionClass": "toast-bottom-left",
              "preventDuplicates": false,
              "showDuration": "3000",
              "hideDuration": "1000000",
              "timeOut": "5000",
              "extendedTimeOut": "1000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
          }
          toastr[type](message);
          if(redirectStatus){        
              setTimeout(function(){ window.location = redirectUrl; }, 5000);
          }
        }
        // toastAlert('success', 'suceess message');
        // toastAlert('error', 'error message');
        // toastAlert('warning', 'warning message');
        // toastAlert('info', 'info message');
    </script>
@endsection
@section('scripts')
    <script src="<?= config('constants.admin_assets_url') ?>assets/js/lead-list.js"></script>
@endsection
