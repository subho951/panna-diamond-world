<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
    {{-- <style>
        .select2-container {
            width: 100% !important;
        }
    </style> --}}
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <h4><?= $page_header ?></h4>
            <h6 class="breadcrumb-wrapper">
                <span class="text-muted fw-light"><a href="<?= url('dashboard') ?>">Dashboard</a> /</span>
                <?= $page_header ?>
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
                    <div class="card-body">
                        <form id="formAccountSettings" action="<?= url($controllerRoute .'/preview') ?>" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="branch_id" class="form-label">Branch <small class="text-danger">*</small></label>
                                    <select id="branch_id" class="select2 form-select" data-allow-clear="true" name="branch_id" autofocus required>
                                        <option value="" selected disabled>Select Branch</option>
                                        <?php if($branches){ foreach($branches as $branch){?>
                                        <option value="<?= $branch->id ?>"><?= $branch->name ?></option>
                                        <?php } }?>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="telecaller_id" class="form-label">Telecaller <small class="text-danger">*</small></label>
                                    <select class="select2 form-select" id="telecaller_id" name="telecaller_id[]" required multiple>
                                        
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lead_title" class="form-label">Lead Title <small class="text-danger">*</small></label>
                                    <input class="form-control" type="text" id="lead_title" name="lead_title" required placeholder="Lead Title" />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="campaign_type_id" class="form-label">Campaign Type</label>
                                    <select class="select2 form-select"  id="campaign_type_id" name="campaign_type_id">
                                        <option value="" selected disabled>Select Campaign Type</option>
                                        <?php if($campaign_types){ foreach($campaign_types as $campaign_type){?>
                                            <option value="<?= $campaign_type->id ?>"><?= $campaign_type->name ?></option>
                                        <?php } }?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="campaign_id" class="form-label">Campaign <small class="text-danger campaign_star"></small></label>
                                    <select class="select2 form-select"  id="campaign_id" name="campaign_id">
                                        <option value="" selected disabled>Select Campaign</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lead_date" class="form-label">Lead Date <small class="text-danger">*</small></label>
                                    <input class="form-control" type="date" id="lead_date" name="lead_date" value="<?= date('Y-m-d') ?>" min="<?=date('Y-m-d')?>" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lead_file" class="form-label">Lead File <small class="text-danger">*</small>
                                        <small class="text-danger">(Only csv file are allowed to upload)</small>
                                        <a href="<?= url('public/uploads/sample-lead-file-v2.csv') ?>" class="text-primary" target="_blank">Sample File</a>
                                    </label>
                                    <input class="form-control" type="file" id="lead_file" name="lead_file" accept=".csv" required />
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-outline-dark btn-sm me-2">Save Changes</button>
                                <a href="<?= url($controllerRoute) ?>" class="btn btn-outline-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Branch</th>
                                        <th>Telecallers</th>
                                        <th>Lead Title</th>
                                        <th>Campaign Type</th>
                                        <th>Campaign</th>
                                        <th>Lead File</th>
                                        <th>Total Leads</th>
                                        <th>Success Leads</th>
                                        <th>Failed Leads</th>
                                        <th>Assigned Leads</th>
                                        <th>Lead Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @dd($leadListArr); --}}
                                    
                                    @foreach($leadListArr as $leadRow)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $leadRow->branch_name }}</td>

                                        <td>
                                            @foreach($leadRow->telecaller_name_arr as $telecaller_name)
                                            {{ $telecaller_name }}<br>
                                            @endforeach
                                        </td>

                                        <td>{{ $leadRow->title }}</td>
                                        <td>{{ $leadRow->campaign_type_name }}</td>
                                        <td>{{ $leadRow->campaign_name }}</td>

                                        <td>
                                            <a href="<?= url($controllerRoute . '/csv-download/' . $leadRow->encodedId) ?>" class="btn btn-sm btn-primary mb-1" title="Download CSV">
                                                <i class="fa-solid fa-file-csv"></i> 
                                            </a>
                                        </td>

                                        <td>{{ $leadRow->total_upload }}</td>
                                        <td>{{ $leadRow->success_upload }}</td>
                                        <td>{{ $leadRow->failed_upload }}</td>
                                        <td>{{ $leadRow->total_assigned }}</td>
                                        <td>{{ $leadRow->lead_date }}</td>
                                        
                                        <td>
                                            <a href="<?= url($controllerRoute . '/delete/' . $leadRow->encodedId) ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('You Won\'t Be Able To Revert This Action. Are you sure?')"
                                                title="Delete">
                                             <i class="fa-solid fa-trash"></i>
                                            </a>
                                       </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="<?= config('constants.admin_assets_url') ?>assets/js/upload-lead.js"></script>
@endsection
