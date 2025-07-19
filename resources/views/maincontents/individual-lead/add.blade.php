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
                        <form id="formAccountSettings" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id" class="form-label">Branch <small class="text-danger">*</small></label>
                                    <select id="branch_id" class="select2 form-select" data-allow-clear="true" name="branch_id" autofocus required>
                                        <option value="" selected disabled>Select Branch</option>
                                        <?php if($branches){ foreach($branches as $branch){?>
                                        <option value="<?= $branch->id ?>"><?= $branch->name ?></option>
                                        <?php } }?>
                                    </select>
                                </div>
                                @if(session('user_data')['role_id'] == 3)
                                    <input type="hidden" name="telecaller_id" value="{{session('user_data')['user_id']}}">
                                @else
                                <div class="col-md-6 mb-3">
                                    <label for="telecaller_id" class="form-label">Telecaller <small class="text-danger">*</small></label>
                                    <select class="select2 form-select" id="telecaller_id" name="telecaller_id" required >
                                        <option value="" selected disabled>Select Telecaller</option>
                                    </select>
                                </div>
                                @endif


                                <div class="col-md-6 mb-3">
                                    <label for="campaign_type_id" class="form-label">Campaign Type</label>
                                    <select class="select2 form-select" id="campaign_type_id" name="campaign_type_id">
                                        <option value="" selected disabled>Select Campaign Type</option>
                                        <?php if($campaign_types){ foreach($campaign_types as $campaign_type){?>
                                            <option value="<?= $campaign_type->id ?>"><?= $campaign_type->name ?></option>
                                        <?php } }?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="campaign_id" class="form-label">Campaign <small class="text-danger campaign_star"></small></label>
                                    <select class="select2 form-select" id="campaign_id" name="campaign_id">
                                        <option value="" selected disabled>Select Campaign</option>
                                    </select>
                                </div>
                               
                            </div>
                             
                            {{-- lead fields --}}
                            <div class="card mb-4 mt-4">
                                <div class="card-body">
                                    <div class="row">
                                        <h5 class="card-title mb-3">Lead Details</h5>

                                        {{-- @dd($lead_headers); --}}
                                        
                                            @foreach($lead_headers as $leadHeaderRow)

                                                @if($leadHeaderRow->input_type == 'TEXTBOX')  
                                                    @if($leadHeaderRow->slug == 'phone')                                                   
                                                        <div class="col-md-6 mb-3">
                                                            <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} <small class="text-danger">*</small></label>
                                                            <input 
                                                            type="tel" 
                                                            minlength="10" 
                                                            maxlength="10" 
                                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                            class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required placeholder="Enter {{$leadHeaderRow->name}}">
                                                        </div> 
                                                    @elseif($leadHeaderRow->slug == 'email')
                                                        <div class="col-md-6 mb-3">
                                                            <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} <small class="text-danger">*</small></label>
                                                            <input
                                                            type="email"
                                                            oninput="this.value = this.value.toLowerCase();" 
                                                            onblur="if(this.value!=='' && !/^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(this.value)){ 
                                                                alert('Please enter a valid email address'); 
                                                                this.value=''; 
                                                                this.focus(); 
                                                            }"
                                                            class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required placeholder="Enter {{$leadHeaderRow->name}}">
                                                        </div>
                                                    @else
                                                        <div class="col-md-6 mb-3">
                                                            <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} <small class="text-danger">*</small></label>
                                                            <input type="text" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required placeholder="Enter {{$leadHeaderRow->name}}">
                                                        </div>                                                
                                                    @endif                                                              
                                                @endif

                                                
                                                
                                                @if($leadHeaderRow->input_type == 'TEXTAREA')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} <small class="text-danger">*</small></label>
                                                    <textarea class="form-control" rows="1" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required placeholder="Enter {{$leadHeaderRow->name}}"></textarea>
                                                </div>
                                                @endif
                                                
                                                @if($leadHeaderRow->input_type == 'DROPDOWN')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}}<small class="text-danger">*</small></label>
                                                    <select class="select2 form-select" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required>
                                                        <option value="" selected disabled>Select {{$leadHeaderRow->name}}</option>
                                                        @if($leadHeaderRow->slug == 'country')
                                                            @foreach($country as $countryRow)
                                                                <option value="{{$countryRow->name}}">{{$countryRow->name}}</option>
                                                            @endforeach
                                                        @endif

                                                        @if($leadHeaderRow->slug == 'phone-code')
                                                            @foreach($country as $countryRow)
                                                                <option value="{{$countryRow->phone_code}}">{{$countryRow->phone_code}}</option>
                                                            @endforeach
                                                        @endif

                                                        @if($leadHeaderRow->slug == 'state')
                                                            {{-- @foreach($state as $stateRow)
                                                                <option value="{{$stateRow->name}}">{{$stateRow->name}}</option>
                                                            @endforeach --}}
                                                        @endif

                                                        @if($leadHeaderRow->slug == 'source')
                                                            @foreach($source as $sourceRow)
                                                                <option value="{{$sourceRow->name}}">{{$sourceRow->name}}</option>
                                                            @endforeach
                                                        @endif

                                                    </select>
                                                </div>
                                                @endif
                                                
                                                @if($leadHeaderRow->input_type == 'CHECKBOX')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} <small class="text-danger">*</small></label>
                                                    <input type="checkbox" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required>
                                                </div>
                                                @endif
                                                
                                                @if($leadHeaderRow->input_type == 'RADIO')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} <small class="text-danger">*</small></label>
                                                    <input type="radio" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required>
                                                </div>
                                                @endif
                                                
                                                @if($leadHeaderRow->input_type == 'DATE')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} <small class="text-danger">*</small></label>
                                                    <input type="date" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required>
                                                </div>
                                                @endif
                                                
                                                @if($leadHeaderRow->input_type == 'TIME')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} <small class="text-danger">*</small></label>
                                                    <input type="time" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" required>
                                                </div>
                                                @endif

                                            @endforeach
                                       

                                    </div>
                                </div>
                            </div>


                            <div class="mt-4">
                                <button type="submit" class="btn btn-outline-dark btn-sm me-2">Save Changes</button>
                                <a href="<?= url($controllerRoute . '/add') ?>" class="btn btn-outline-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="<?= config('constants.admin_assets_url') ?>assets/js/add-individual-lead.js"></script>
@endsection
