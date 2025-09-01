<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .swal2-container{
        z-index: 9999 !important;
    }
</style>

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

                        {{-- <h6 class="card-title">Filter</h6> --}}
                        {{-- filter section --}}
                        <div class="card mb-3 p-3" >
                            <form>
                                @csrf
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="selected_branch_id" class="form-label">Branch </label>
                                                <select id="selected_branch_id" class="select2 form-select" name="selected_branch_id" >
                                                    <option value="" selected disabled>Select Branch</option>
                                                    @if(!empty($allBranches))
                                                       @foreach($allBranches as $branch)
                                                           <option
                                                            value="{{ Helper::encoded($branch->id) }}"
                                                     
                                                            @if(!empty($selected_branch_id))                                                  
                                                                @if(Helper::encoded($branch->id) == Helper::encoded($selected_branch_id))
                                                                    selected
                                                                @endif                                                     
                                                            @endif >{{ $branch->name }}
                                                           </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="selected_telecaller_id" class="form-label">Telecaller </label>
                                                <select id="selected_telecaller_id" class="select2 form-select" name="selected_telecaller_id">
                                                    <option value="" disable selected>Select Telecaller</option>
                                                    @if(!empty($branchWiseTelecaller))
                                                       @foreach($branchWiseTelecaller as $telecaller)
                                                           <option
                                                            value="{{ Helper::encoded($telecaller->id) }}"
                                                            @if(!empty($selected_telecaller_id))
                                                                @if(Helper::encoded($telecaller->id) == Helper::encoded($selected_telecaller_id))
                                                                   selected
                                                                @endif
                                                            @endif
                                                            >{{ $telecaller->first_name }} {{ $telecaller->last_name }}</option>
                                                       @endforeach
                                                    @elseif(!empty($allTelecallers))
                                                       @foreach($allTelecallers as $telecaller)
                                                           <option
                                                            value="{{ Helper::encoded($telecaller->id) }}"
                                                            @if(!empty($selected_telecaller_id))
                                                                @if(Helper::encoded($telecaller->id) == Helper::encoded($selected_telecaller_id))
                                                                   selected
                                                                @endif
                                                            @endif
                                                            >{{ $telecaller->first_name }} {{ $telecaller->last_name }}</option>
                                                       @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="parent_status_id" class="form-label">Parent Status </label>
                                                <select id="parent_status_id" class="select2 form-select" name="parent_status_id">
                                                    <option value="" disable selected>Select Parent Status</option>
                                                    @if(!empty($allParentStatus))
                                                        @foreach($allParentStatus as $parentStatus)
                                                         <option
                                                            value="{{ Helper::encoded($parentStatus->id) }}"
                                                            @if(!empty($selected_parent_status_id))
                                                                @if(Helper::encoded($parentStatus->id) == Helper::encoded($selected_parent_status_id))
                                                                    selected
                                                                @endif   
                                                            @endif
                                                          >{{ $parentStatus->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="child_status_id" class="form-label">Child Status </label>
                                                <select id="child_status_id" class="select2 form-select" name="child_status_id">
                                                    <option value="" disable selected>Select Child Status</option>
                                                    @if(!empty($parentWiseChildStatus))
                                                        @foreach($parentWiseChildStatus as $childStatus)
                                                        <option
                                                            value="{{ Helper::encoded($childStatus->id) }}"
                                                            @if(!empty($selected_child_status_id))
                                                                @if(Helper::encoded($childStatus->id) == Helper::encoded($selected_child_status_id))
                                                                    selected
                                                                @endif   
                                                            @endif
                                                        >{{ $childStatus->name }}</option>
                                                        @endforeach
                                                    @elseif(!empty($allChildStatus))
                                                        @foreach($allChildStatus as $childStatus)
                                                        <option
                                                            value="{{ Helper::encoded($childStatus->id) }}"
                                                            @if(!empty($selected_child_status_id))
                                                                @if(Helper::encoded($childStatus->id) == Helper::encoded($selected_child_status_id))
                                                                    selected
                                                                @endif   
                                                            @endif
                                                        >{{ $childStatus->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <div class="row">
                                            <div class="col-md-12 d-flex gap-2 mt-3">
                                                <button type="button" class="btn btn-outline-dark filterBtn">
                                                    <i class="fa-solid fa-filter"></i>&nbsp;<span>Filter</span>
                                                </button>
                                                <button type="button" class="btn btn-label-secondary d-none resetBtn">
                                                    <i class="fa-solid fa-arrow-rotate-left"></i>&nbsp;<span>Reset</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                       
                            </form>
                        </div>
                        
                        {{-- bulk lead transfer section --}}
                        @if(session('user_data')['role_id'] != 3)
                            @if(!empty($totalLeadArr))
                                <div class="card mb-3 p-3 bulkTransferSection" style="display: none;">
                                    <form class="row d-flex align-items-center">
                                        @csrf
                                        <div class="col-md-3 mb-3">
                                            <label for="transfer_branch_id" class="form-label">Transfer To Branch </label>
                                            <select id="transfer_branch_id" class="select2 form-select" name="transfer_branch_id" >
                                                <option value="" selected disabled>Select Branch</option>
                                                <?php if(!empty($allBranches)){ foreach($allBranches as $branch){?>
                                                <option value="{{ Helper::encoded($branch->id) }}"
                                                @if(!empty($selected_branch_id))                                                  
                                                    @if(Helper::encoded($branch->id) == Helper::encoded($selected_branch_id))
                                                        selected
                                                    @endif                                                     
                                                @endif
                                                ><?= $branch->name ?></option>
                                                <?php } }?>
                                            </select>
                                        </div>                                   
                                        <div class="col-md-6 mb-3">
                                            <label for="transfer_telecaller_id" class="form-label">Transfer To Telecaller(s)  </label>
                                            <select class="select2 form-select" id="transfer_telecaller_id" name="transfer_telecaller_id[]" multiple>
                                                @if(!empty($branchWiseTelecaller))
                                                        @foreach($branchWiseTelecaller as $telecaller)
                                                            <option value="{{ Helper::encoded($telecaller->id) }}">{{ $telecaller->first_name }} {{ $telecaller->last_name }}</option>
                                                        @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <button type="button" class="btn btn-outline-dark w-100 rounded-pill bulkLeadTransferBtn">
                                                <i class="fas fa-exchange-alt"></i>&nbsp;<span>Bulk Lead Transfer</span>
                                            </button>
                                        </div>
                                
                                    </form>                                
                                </div>
                            @endif
                        @endif
                         
                        {{-- lead list section --}}
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
                                        
                                                    <th><input type="checkbox" id="selectAll"></th>

                                            @endif
                                            <th>#</th>
                                            <th>Lead No</th>
                                            <th>Details</th>
                                            <th>Last Activity</th>
                                            <th>Next Schedule</th>
                                            <th>
                                                @if(session('user_data')['role_id'] != 3)
                                                    Telecaller | Branch |
                                                @endif
                                                Campaign
                                            </th>
                                            <th style="text-align: center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">

                                        @if(!empty($totalLeadArr))

                                        @foreach($totalLeadArr as $eachLeadArr)
                                        {{-- @dd($eachLeadArr); --}}
                                        <tr>
                                            
                                                <td>
                                                    <input type="checkbox"
                                                    name="branchLead_id_Arr[]" 
                                                    class="lead-checkbox" 
                                                    value="{{ Helper::encoded($eachLeadArr->id) }}">
                                                </td>
                                            


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
                                            <td>
                                                @if(session('user_data')['role_id'] != 3)
                                                
                                                    @if(!empty($eachLeadArr->assigned_telecaller_name))
                                                        <span class="badge badge-center rounded-pill bg-label-danger mt-1">
                                                            <i class="fa-solid fa-user-tie"></i>
                                                        </span> <span style="font-size: 11px;">{{ $eachLeadArr->assigned_telecaller_name }}</span>
                                                        <br>
                                                    @endif
                                                    @if(!empty($eachLeadArr->branch_name))
                                                        <span class="badge badge-center rounded-pill bg-label-secondary mt-1">
                                                            <i class="fas fa-sitemap"></i>
                                                        </span> <span style="font-size: 11px;">{{ $eachLeadArr->branch_name }}</span>
                                                        <br>
                                                    @endif
                                                @endif

                                                @if(!empty($eachLeadArr->campaign_type_name) && !empty($eachLeadArr->campaign_name))
                                                   @if(!empty($eachLeadArr->campaign_type_name))
                                                        <span class="badge bg-label-primary mt-1" style="font-size: 8px;">{{ $eachLeadArr->campaign_type_name }}</span>
                                                        <br>
                                                   @endif
                                                   @if(!empty($eachLeadArr->campaign_name))
                                                        <span class="badge bg-label-primary mt-1 mb-1" style="font-size: 8px;">{{ $eachLeadArr->campaign_name }}</span>
                                                        
                                                   @endif
                                                @endif
                                            </td>
                                            
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
                                                    <button
                                                        class="individualLeadTransferButton btn btn-sm btn-outline-dark mb-1"
                                                        title="Transfer Lead To Another Telecaller"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#individualLeadTransferModal"
                                                        data-id="{{ Helper::encoded($eachLeadArr->id) }}">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
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

                        {{-- individual lead transfer modal --}}
                        <div class="modal fade" id="individualLeadTransferModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Transfer Individual Lead</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
        
                                    <div class="modal-body pb-0">
                                        
                                    </div>                             
                                </div>
                            </div>
                        </div>
                        {{-- individual lead transfer modal --}}

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

        // Pagination
        // Pagination (guard if element is absent)
        const perPageEl = document.getElementById('perPageSelect');
        if (perPageEl)
        {
            perPageEl.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('perPage', this.value);
                url.searchParams.set('page', 1); // reset to first page
                window.location.href = url.toString();
            });
        }

       
         
    
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


        // individual lead transfer modal
        $(document).on('click', '.individualLeadTransferButton', function() {
            id = $(this).data('id'); // BranchLead ID

            $.ajax({
                url: base_url + '/lead-list/individual-lead-transfer-modal-data',
                type: 'POST',
                data: {
                    branchLead_id: id
                },
                success: function(response) {
                    $('#individualLeadTransferModal .modal-body').html(response.html);
                    $('#individualLeadTransferModal').modal('show'); // force show

                    // console.log(response);
                },
                error: function(xhr) {
                    $('#individualLeadTransferModal .modal-body').html('');
                    $('#individualLeadTransferModal').modal('hide'); // force hide
                    alert('Error loading lead data.');
                    console.log(xhr);
                }
            });
        });

        
        // individual lead transfer
        $(document).on('submit', '#individualLeadTransferForm', function (e) 
        {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const telecallerSelect = form.find('select[name="to_assigned_telecaller_id"]');
            const checkboxes = form.find('input[name="branchLead_id_Arr[]"][type="checkbox"]');

            // campaignLength may be absent; fall back to checkbox count
            const rawLen = form.find('input[name="campaignLength"]').val();
            const campaignLength = Number.isFinite(parseInt(rawLen, 10))
                ? parseInt(rawLen, 10)
                : checkboxes.length;

            // Validation logic
            const telecallerVal = (telecallerSelect.val() || '').trim();
            const checkedCount = checkboxes.filter(':checked').length;

            if (!telecallerVal && campaignLength > 1 && checkedCount === 0) {
                toastAlert('error', 'Please select a telecaller and at least one campaign !!!');
                return;
            }

            if (!telecallerVal) {
                toastAlert('error', 'Please select a telecaller !!!');
                return;
            }

            if (campaignLength > 1 && checkedCount === 0) {
                toastAlert('error', 'Please select at least one campaign !!!');
                return;
            }

            
            let leadTransferData = new FormData(this); 

            $.ajax({
                url: base_url + '/lead-list/individual-lead-transfer',
                type: 'POST',
                data: leadTransferData,
                processData: false,
                contentType: false, 
                success: function (resp) 
                {                 
                    // console.log(resp);

                    if(resp.status == 'success')
                    {
                        toastAlert('success', resp.message);
                        $('#individualLeadTransferModal').modal('hide'); // force hide

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                    else if(resp.status == 'error')
                    {
                        toastAlert('error', resp.message);
                    }
                },
                error: function (xhr) {
                    // toastAlert('error', 'Failed to submit. Please try again.');
                    console.log(xhr);
                },

            });
        });

        

        // select branch
        let selected_branch_id = "";
        $(document).on('change', '#selected_branch_id', function()
        {
            selected_branch_id = $(this).val();

            $('#selected_telecaller_id').val('').trigger('change');
            $.ajax({
                url: base_url + '/lead-list/fetch-branch-wise-telecaller',
                type: 'POST',
                data: {
                    selected_branch_id : selected_branch_id ,
                },
                success: function(resp)
                {
                    $('#selected_telecaller_id').empty();
                    $('#selected_telecaller_id').append(`<option value="" disable selected>Select Telecaller</option>`);
                    resp.forEach(telecaller => {
                        $('#selected_telecaller_id').append(`<option value="${telecaller.id}">${telecaller.first_name} ${telecaller.last_name}</option>`);
                    });
                    
                },
                error: function(xhr)
                {
                    console.log(xhr);
                }
            });      
        });
        // select telecaller
        let selected_telecaller_id = "";
        $(document).on('change', '#selected_telecaller_id', function(){
            selected_telecaller_id = $(this).val();
        });

        let branchFromUrl = "";
        let urlParams = new URLSearchParams(window.location.search);
        branchFromUrl = urlParams.get('branch'); // get branch from url 
        // console.log('branch:', branchFromUrl); // showing correctly
        
        //select parent status
        let selected_parent_status_id = ""
        $(document).on('change', '#parent_status_id', function(){
            selected_parent_status_id = $(this).val();

            $('#child_status_id').val('').trigger('change');
            $.ajax({
                url: base_url + '/lead-list/fetch-parent-wise-child-status' ,
                type: 'POST',
                data: {
                    parent_status_id : selected_parent_status_id ,
                },
                success: function(resp)
                {
                    $('#child_status_id').empty();
                    $('#child_status_id').append(`<option value="" disable selected>Select Child Status</option>`);
                    resp.forEach(parentStatus => {
                        $('#child_status_id').append(`<option value="${parentStatus.id}" > ${parentStatus.name}</option>`);
                    });
                },
                error: function(xhr)
                {
                    console.log(xhr);
                }
                
            });
        });

        //select child status
        let selected_child_status_id = "";
        $(document).on('change', '#child_status_id', function(){
            selected_child_status_id = $(this).val();
        });



        // filter
        $(document).on('click', '.filterBtn', function()
        {
            // Get values of all filter dropdowns
            let branch = $('#selected_branch_id').val();
            let telecaller = $('#selected_telecaller_id').val();
            let parentStatus = $('#parent_status_id').val();
            let childStatus = $('#child_status_id').val();

            // Check if all are empty
            if ((!branch || branch === "") &&
                (!telecaller || telecaller === "") &&
                (!parentStatus || parentStatus === "") &&
                (!childStatus || childStatus === "")) {

                toastAlert('error', 'Please Select Something To Apply Filter');
            }
            else
            {
                let url = new URL(window.location.href);
    
                if(selected_branch_id != "")
                {
                    // alert('set branch with: '+ selected_branch_id + ' or ' + branchFromUrl);
                    url.searchParams.set('branch', selected_branch_id);
                    url.searchParams.delete('telecaller');
                }
    
                if(selected_telecaller_id != "")
                {
                    // alert('set telecaller with: '+ selected_telecaller_id);
                    url.searchParams.set('telecaller', selected_telecaller_id);
                }

                if(selected_parent_status_id != "")
                {
                //    alert('set parent status with: '+ selected_parent_status_id);
                   url.searchParams.set('parent-status', selected_parent_status_id);
                   url.searchParams.delete('child-status');
                }

                if(selected_child_status_id != "")
                {
                    // alert('set child status with: '+ selected_child_status_id);                   
                    url.searchParams.set('child-status', selected_child_status_id);
                }


                // Always reset to page 1
                url.searchParams.set('page', 1);

                // Redirect once
                window.location.href = url.toString();
            }




            

            
        });

        // show reset button iff any filter is applied
        const currentUrl = new URL(window.location.href);
        if( (currentUrl.searchParams.has('branch')) || (currentUrl.searchParams.has('telecaller')) || (currentUrl.searchParams.has('parent-status')) || (currentUrl.searchParams.has('child-status')) )
        {
            $('.resetBtn').removeClass('d-none');
            // $('.resetBtn').addClass('d-block');
        }

        // reset
        $(document).on('click', '.resetBtn', function()
        {
            let url = new URL(window.location.href);
            // safe even if it's not there
            url.searchParams.delete('branch');
            url.searchParams.delete('telecaller');
            url.searchParams.delete('parent-status');
            url.searchParams.delete('child-status');


            url.searchParams.set('page', 1); // reset to first page
            window.location.href = url.toString();
        });




        // show bulk lead transfer section if atleast one checkbox is checked
        function toggleBulkTransferSection() 
        {
            if ($('input[name="branchLead_id_Arr[]"]:checked').length > 0) {
                // show with animation
                // $('.bulkTransferSection').slideDown(); 
                $('.bulkTransferSection').fadeIn(300); 
            } else {
                // hide with animation
                // $('.bulkTransferSection').slideUp(); 
                $('.bulkTransferSection').fadeOut(300);
            }
        }

        // When any checkbox changes
        $(document).on('change', 'input[name="branchLead_id_Arr[]"]', function() {
            toggleBulkTransferSection();
        });

        // "Select All" checkbox
        $('#selectAll').on('change', function() {
            $('input[name="branchLead_id_Arr[]"]').prop('checked', this.checked);
            toggleBulkTransferSection();
        });

        // fetch branch wise telecaller for lead transfer
        let transfer_branch_id = "";
        $(document).on('change', '#transfer_branch_id', function()
        {
            transfer_branch_id = $(this).val();
            
            $('#transfer_telecaller_id').val('').trigger('change');
            $.ajax({
                url: base_url + '/lead-list/fetch-branch-wise-telecaller',
                type: 'POST',
                data: {
                    selected_branch_id :  transfer_branch_id,
                },
                success: function(resp)
                {
                    $('#transfer_telecaller_id').empty();
                    resp.forEach(telecaller => {
                        $('#transfer_telecaller_id').append(`<option value="${telecaller.id}">${telecaller.first_name} ${telecaller.last_name}</option>`);
                    });
                    
                },
                error: function(xhr)
                {
                    console.log(xhr);
                }
            });  
            
        });

        
        
        // transfer bulk lead
        $(document).on('click', '.bulkLeadTransferBtn', function()
        {   
            if (branchFromUrl !== null && branchFromUrl !== "") 
            {
                let transfer_branch = "";
                transfer_branch = $('#transfer_branch_id').val();
                if(transfer_branch != "")
                {
                    let transfer_telecaller_id_arr = "";
                    transfer_telecaller_id_arr = $('#transfer_telecaller_id').val();
                    
                    if(transfer_telecaller_id_arr != "" && transfer_telecaller_id_arr.length > 0)
                    {
                        if($('input[name="branchLead_id_Arr[]"]:checked').length > 0)
                        {
                            // jQuery collection of checked checkboxes
                            let checkedCheckboxes = $('input[name="branchLead_id_Arr[]"]:checked');    

                            // Array of values from those checkboxes
                            let branchLead_id_Arr = checkedCheckboxes.map(function()
                            {
                                return $(this).val();
                            }).get();

                            // console.log("Selected values:", branchLead_id_Arr); 

                            let transfer_telecaller_name_arr = [];

                            $('#transfer_telecaller_id option:selected').each(function() {
                                transfer_telecaller_name_arr.push($(this).text().trim());
                            });

                            let transfer_telecaller_name_str = "";

                            if (transfer_telecaller_id_arr && transfer_telecaller_id_arr.length > 0) 
                            {
                                transfer_telecaller_name_str = transfer_telecaller_name_arr.join(", ");
                            }
                            
                            Swal.fire({
                            text: `Transfer ${$('input[name="branchLead_id_Arr[]"]:checked').length} lead(s) to ${transfer_telecaller_name_str} ?`,
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonColor: "#000000",
                            cancelButtonColor: "#ff4c51",
                            confirmButtonText: "Confirm"
                            }).then((result) => {
                            if (result.isConfirmed) 
                            {
                                $.ajax({
                                    url: base_url + '/lead-list/bulk-lead-transfer',
                                    type: 'POST',
                                    data: {
                                        branchFromUrl : branchFromUrl ,
                                        transfer_branch : transfer_branch ,
                                        branchLead_id_Arr : branchLead_id_Arr,
                                        transfer_telecaller_id_arr : transfer_telecaller_id_arr ,
                                    },
                                    success: function(resp)
                                    {
                                        if(resp.status == 'success')
                                        {
                                            toastAlert('success', resp.message);

                                            setTimeout(() => {
                                                location.reload();
                                            }, 1000);
                                        }
                                        else if(resp.status == 'error')
                                        {
                                            toastAlert('error', resp.message);
                                        }
                                    },
                                    error: function(xhr)
                                    {
                                        console.log(xhr);
                                    }
                                });

                            }
                            });
                        }
                        else if($('input[name="branchLead_id_Arr[]"]:checked').length == 0)
                        {
                            toastAlert('error', 'Please Select At Least One Lead To Transfer !!!');
                        }
                    }
                    else if(transfer_telecaller_id_arr == "")
                    {
                        toastAlert('error', 'Please Select "Transfer To Telecaller(s)" !!!');
                    }
                }
                else if(transfer_branch == "")
                {
                    toastAlert('error', 'Please Select "Transfer To Branch" !!!');
                }

            }
            else if (branchFromUrl === null || branchFromUrl === "") 
            {
                toastAlert('error', 'Please Filter By Branch !!!');
            }
                    
            
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
