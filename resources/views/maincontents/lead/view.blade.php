<?php
use App\Models\LeadStatus;
use App\Models\CampaignType;
use App\Models\Campaign;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\LeadTransfer;

$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row g-6">
        <h4><?= $page_header ?></h4>
        <h6 class="breadcrumb-wrapper">
            <span class="text-muted fw-light"><a href="<?= url('dashboard') ?>">Dashboard</a> /</span>
            <span class="text-muted fw-light"><a href="<?= url($controllerRoute) ?>"><?= $module['title'] ?> List</a> /</span>
            <?= $page_header ?>
        </h6>
        <div class="nav-align-top mb-4">
            <?php if (session('success_message')) { ?>
                <div class="alert alert-success alert-dismissible autohide" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-desktop align-top me-2"></i>Success!</h6>
                    <span><?= session('success_message') ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
            <?php } ?>
            <?php if (session('error_message')) { ?>
                <div class="alert alert-danger alert-dismissible autohide" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-store align-top me-2"></i>Error!</h6>
                    <span><?= session('error_message') ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
            <?php } ?>
            <div class="card mb-4">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-profile" aria-controls="navs-pills-justified-profile" aria-selected="true"><i class="tf-icons bx bx-home me-1"></i> Basic Details</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-general" aria-controls="navs-pills-justified-general" aria-selected="false"><i class="tf-icons bx bx-user me-1"></i> Activity Details</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-password" aria-controls="navs-pills-justified-password" aria-selected="false"><i class="tf-icons bx bx-lock me-1"></i> Transfer Details</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-email" aria-controls="navs-pills-justified-email" aria-selected="false"><i class="tf-icons bx bx-envelope me-1"></i> Update Request Details</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-profile" role="tabpanel">
                            <h5>Basic Details</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary mb-1 leadActivity">View Details Of: <span class="badge bg-label-primary">{{ $leadActivityCount["lead_no"] }}</span></h5>

                                    {{-- Campaign Card --}}
                                    <div class="card mb-3">
                                        <div class="card-body p-1">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr class="mb-1">
                                                            <th style="border: none; width: 50%;">Campaign Type | Campaign</th>
                                                            <th style="border: none; width: 50%;">Added By | Added On</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- @dd($totalCampaigns); --}}
                                                        @foreach($totalCampaigns as $campaignArr)
                                                        {{-- @dd($campaignArr); --}}
                                                        <tr>
                                                            <th style="width: 50%; border: none; border-top-left-radius: 4px;border-bottom-left-radius: 4px;">
                                                                <span class="badge bg-label-primary mb-1" style="font-size: 10px;">{{ $campaignArr["campaign_type_name"] }}</span>
                                                                <br>
                                                                <span class="badge bg-label-primary mb-1" style="font-size: 10px;">{{ $campaignArr["campaign_name"] }}</span>
                                                            </th>
                                                            <td style="width: 50%;" class="text-primary">
                                                                <p class="mb-1"> {{ $campaignArr["added_by_name"] }} </p>
                                                                <p class="mb-1"> {{ $campaignArr["created_at"] }} </p>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Lead Details Card --}}
                                    <div class="card">
                                        <div class="card-body p-1">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr class="mb-2">
                                                            <th style="border: none; width: 40%;">Name</th>
                                                            <th style="border: none; width: 60%;">Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- @dd($eachLeadArr); --}}
                                                        @foreach ($eachLeadArr as $leadData)
                                                        {{-- @dd($leadData); --}}
                                                        @foreach($leadData as $key => $value)
                                                        <tr class="mb-2">
                                                            <th class="" style="width: 40%; font-size: 10px; border: none; border-top-left-radius: 4px;border-bottom-left-radius: 4px; color: black;">{{ $key }}</th>
                                                            <td class="text-primary" style="width: 60%; font-size: 12px;">{{ $value }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-justified-general" role="tabpanel">
                            <h5>Activity Details</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-primary mb-1 mt-2 leadActivity text-center">Total Lead Activity: {{ $leadActivityCount["lead_activity_count"]}} </h6>

                                    <div class="card" style="height: 750px; overflow-y: auto;">
                                        <div class="leadHistoryContainer card-body p-1">

                                            @if(!empty($leadHistoryArr) && count($leadHistoryArr) > 0)
                                            @foreach($leadHistoryArr as $leadHistoryRow)
                                            @php
                                            $statusBadgesHTML = '';

                                            if (!empty($leadHistoryRow['parentStatus']['background_color']) && !empty($leadHistoryRow['parentStatus']['font_color']) && !empty($leadHistoryRow['parentStatus']['name'])) {
                                            $statusBadgesHTML .= '<span class="badge bg-glow me-1 mt-1 ms-1" style="background-color: ' . e($leadHistoryRow['parentStatus']['background_color']) . '; color: ' . e($leadHistoryRow['parentStatus']['font_color']) . '; font-size: 11px;">' . e($leadHistoryRow['parentStatus']['name']) . '</span>';
                                            }

                                            if (!empty($leadHistoryRow['childStatus']['background_color']) && !empty($leadHistoryRow['childStatus']['font_color']) && !empty($leadHistoryRow['childStatus']['name'])) {
                                            $statusBadgesHTML .= '<span class="badge bg-glow me-1 mt-1" style="background-color: ' . e($leadHistoryRow['childStatus']['background_color']) . '; color: ' . e($leadHistoryRow['childStatus']['font_color']) . '; font-size: 11px;">' . e($leadHistoryRow['childStatus']['name']) . '</span>';
                                            }
                                            @endphp

                                            <div class="bg-label-success rounded p-3 mb-3">
                                                <div class="row align-items-center">
                                                    @if(!empty($leadHistoryRow['last_call']))
                                                    <div class="col">
                                                        <div class="d-flex align-items-center small">
                                                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                                                <i class="fa-solid fa-headset"></i>
                                                            </span>
                                                            <span class="text-primary ms-1" style="font-size: 11px;">
                                                                Last Call: <strong>{{ $leadHistoryRow['last_call'] }}</strong>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(!empty($leadHistoryRow['assigned_telecaller_name']))
                                                    <div class="col-auto text-end">
                                                        <span class="fw-semibold text-primary" style="font-size: 12px;">
                                                            {{ $leadHistoryRow['assigned_telecaller_name'] }}
                                                        </span>
                                                    </div>
                                                    @endif
                                                </div>

                                                @if(!empty($leadHistoryRow['purpose_name']))
                                                <div class="small mt-2">
                                                    <span class="badge badge-center rounded-pill bg-white text-primary">
                                                        <i class="fa-solid fa-note-sticky"></i>
                                                    </span>
                                                    <span class="ms-1 text-primary" style="font-size: 11px;">
                                                        Purpose: <strong>{{ $leadHistoryRow['purpose_name'] }}</strong>
                                                    </span>
                                                </div>
                                                @endif

                                                @if(!empty($leadHistoryRow['campaign_type_name']) && !empty($leadHistoryRow['campaign_name']))
                                                <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                                    <span class="badge badge-center rounded-pill bg-white text-primary">
                                                        <i class="fa-solid fa-bullhorn"></i>
                                                    </span>
                                                    <span class="badge bg-label-dark bg-glow me-1 mt-1 ms-1" style="font-size: 11px;">{{ $leadHistoryRow['campaign_type_name'] }}</span>
                                                    <span class="badge bg-label-dark bg-glow me-1 mt-1" style="font-size: 11px;">{{ $leadHistoryRow['campaign_name'] }}</span>
                                                </div>
                                                @endif

                                                @if($statusBadgesHTML)
                                                <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                                    <span class="badge badge-center rounded-pill bg-white text-primary">
                                                        <i class="fa-solid fa-circle-info"></i>
                                                    </span>
                                                    {!! $statusBadgesHTML !!}
                                                </div>
                                                @endif

                                                @if(!empty($leadHistoryRow['comment']))
                                                <div class="small mt-2">
                                                    <span class="badge badge-center rounded-pill bg-white text-primary">
                                                        <i class="fa-solid fa-comment-dots"></i>
                                                    </span>
                                                    <span class="ms-1" style="font-size: 11px;">
                                                        Comment: <strong>{{ $leadHistoryRow['comment'] }}</strong>
                                                    </span>
                                                </div>
                                                @endif

                                                @if(!empty($leadHistoryRow['mood']['emoji']) && !empty($leadHistoryRow['mood']['name']) && !empty($leadHistoryRow['mood']['color']))
                                                <div class="small mt-2">
                                                    <span class="badge badge-center rounded-pill bg-white text-primary">
                                                        {{ $leadHistoryRow['mood']['emoji'] }}
                                                    </span>
                                                    <span class="ms-1 badge rounded-pill bg-white bg-glow" style="color: {{ $leadHistoryRow['mood']['color'] }}; font-size: 11px;">
                                                        <strong>{{ $leadHistoryRow['mood']['name'] }}</strong>
                                                    </span>
                                                </div>
                                                @endif

                                                @if(!empty($leadHistoryRow['feedbackTagNameArr']) && is_array($leadHistoryRow['feedbackTagNameArr']))
                                                <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                                    <span class="badge badge-center rounded-pill bg-white text-primary me-1">
                                                        <i class="fa-solid fa-clipboard-list"></i>
                                                    </span>
                                                    @foreach($leadHistoryRow['feedbackTagNameArr'] as $tag)
                                                    <span class="badge bg-glow rounded-pill bg-dark text-white me-1 mt-1" style="font-size: 11px;">{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                                @endif

                                                @if(!empty($leadHistoryRow['next_followup_date']) || !empty($leadHistoryRow['next_followup_time']))
                                                <div class="text-secondary d-flex align-items-center small mt-1">
                                                    <span class="badge badge-center rounded-pill bg-white text-primary">
                                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                                    </span>
                                                    <span class="ms-1" style="font-size: 11px;">
                                                        Next Schedule: <strong>{{ $leadHistoryRow['next_followup_date'] ?? '' }} {{ $leadHistoryRow['next_followup_time'] ?? '' }}</strong>
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                            @else
                                            <div class="d-flex justify-content-center align-items-center" style="height: 100%; min-height: 150px;">
                                                <p class="fw-semibold text-danger m-0" style="font-size: 12px;">No Activity Found.</p>
                                            </div>
                                            @endif


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-justified-password" role="tabpanel">
                            <h5>Transfer Details</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>Transfer From</td>
                                        <td>Transfer To</td>
                                        <td>Campaign Type</td>
                                        <td>Campaign</td>
                                        <td>Parent Status</td>
                                        <td>Child Status</td>
                                        <td>Transfer Timestamp</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sl_no=1 ; if($transfers){ foreach($transfers as $transfer){?>
                                        <?php
                                        $getTransferFrom = User::select('first_name', 'last_name')->where('id', '=', $transfer->from_assigned_telecaller_id)->first();
                                        $getTransferTo = User::select('first_name', 'last_name')->where('id', '=', $transfer->to_assigned_telecaller_id)->first();
                                        $getCampaignType = CampaignType::select('name')->where('id', '=', $transfer->campaign_type_id)->first();
                                        $getCampaign = Campaign::select('name')->where('id', '=', $transfer->campaign_id)->first();
                                        $getParentStatus = LeadStatus::select('name')->where('id', '=', $transfer->parent_status_id)->first();
                                        $getChildStatus = LeadStatus::select('name')->where('id', '=', $transfer->child_status_id)->first();
                                        ?>
                                        <tr>
                                            <td><?=$sl_no++?></td>
                                            <td><?=(($getTransferFrom)?$getTransferFrom->first_name.' '.$getTransferFrom->last_name:'')?></td>
                                            <td><?=(($getTransferTo)?$getTransferTo->first_name.' '.$getTransferTo->last_name:'')?></td>
                                            <td><?=(($getCampaignType)?$getCampaignType->name:'')?></td>
                                            <td><?=(($getCampaign)?$getCampaign->name:'')?></td>
                                            <td><?=(($getParentStatus)?$getParentStatus->name:'')?></td>
                                            <td><?=(($getChildStatus)?$getChildStatus->name:'')?></td>
                                            <td><?=date_format(date_create($transfer->created_at), "d-m-Y h:i:s A")?></td>
                                        </tr>
                                    <?php } } else {?>
                                        <tr>
                                            <td colspan="8" style="color:red; text-align:center;">No records found</td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-justified-email" role="tabpanel">
                            <h5>Update Request</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>Request From</td>
                                        <td>Request Comment</td>
                                        <td>Request Timestamp</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sl_no=1 ; if($update_requests){ foreach($update_requests as $update_request){?>
                                        <tr>
                                            <td><?=$sl_no++?></td>
                                            <td><?=$update_request->first_name.' '.$update_request->last_name?></td>
                                            <td><?=$update_request->request_comment?></td>
                                            <td><?=date_format(date_create($update_request->created_at), "d-m-Y h:i:s A")?></td>
                                        </tr>
                                    <?php } } else {?>
                                        <tr>
                                            <td colspan="4" style="color:red; text-align:center;">No records found</td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="<?= config('constants.admin_assets_url') ?>assets/js/table.js"></script>
@endsection