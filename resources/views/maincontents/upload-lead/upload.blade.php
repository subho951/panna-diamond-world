<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
<style>
        
    .select2-container {
        width: 100% !important;
    }
</style>
    <div class="container-xxl flex-grow-1 container-p-y">
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
                    {{-- <?php
                    if ($row) {
                        
                    } else {
                        
                    }
                    ?> --}}
                    <div class="card-body">
                        <form id="formAccountSettings" action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id" class="form-label">Branch <small
                                            class="text-danger">*</small></label>
                                    <select class="form-control" type="text" id="branch_id" name="branch_id" autofocus
                                        required>
                                        <option value="" selected disabled>Select Branch</option>
                                        <?php if($branches){ foreach($branches as $branch){?>
                                        <option value="<?= $branch->id ?>"><?= $branch->name ?></option>
                                        <?php } }?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telecaller_id" class="form-label">Telecaller <small
                                            class="text-danger">*</small></label>
                                    <select class="form-control select2" id="telecaller_id" name="telecaller_id[]"
                                        required multiple>
                                        <option value="" selected disabled>Select Telecaller</option>                                       

                                    </select>
                                </div>
                                
                                
                                

                                <div class="col-md-6 mb-3">
                                    <label for="campaign_type_id" class="form-label">Campaign Type</label>
                                    <select class="form-control" type="text" id="campaign_type_id"
                                        name="campaign_type_id">
                                        <option value="" selected disabled>Select Campaign Type</option>
                                        <?php if($campaign_types){ foreach($campaign_types as $campaign_type){?>
                                        <option value="<?= $campaign_type->id ?>"><?= $campaign_type->name ?></option>
                                        <?php } }?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="campaign_id" class="form-label">Campaign <small
                                            class="text-danger campaign_star"></small></label>
                                    <select class="form-control" type="text" id="campaign_id" name="campaign_id">
                                        <option value="" selected disabled>Select Campaign</option>
                                       
                                    </select>
                                </div>

                              
                                
                                 
                                
                                

                                {{-- <div class="col-md-6 mb-3">
                            <label for="status" class="form-label d-block">Status <small class="text-danger">*</small></label>
                            <div class="form-check form-switch mt-0 ">
                                <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" <?= $status == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>  --}}

                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary btn-sm me-2">Save Changes</button>
                                <a href="<?= url($controllerRoute . '/list/') ?>"
                                    class="btn btn-label-secondary btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="<?=config('constants.admin_assets_url')?>assets/js/upload-lead.js"></script>
@endsection
