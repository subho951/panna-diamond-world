<?php
use App\Helpers\Helper;
use Carbon\Carbon;
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
                           
                            {{-- lead fields --}}
                            <div class="row">
                                {{-- <h5 class="card-title mb-3">Lead Details</h5> --}}
                                                
                                @foreach($lead_headers as $leadHeaderRow)
                                
                                    {{-- @dd($row); --}}

                                    {{-- @foreach($row as $key => $value)
                                        @foreach($value as $header_name => $header_value)
                                            @if($leadHeaderRow->slug == $header_name)
                                            value="{{$header_value}}"
                                            @endif
                                        @endforeach
                                    @endforeach --}}

                                        @if($leadHeaderRow->input_type == 'TEXTBOX')  
                                            @if($leadHeaderRow->slug == 'phone')                                                   
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                                    <input 
                                                    @foreach($row as $key => $value)
                                                        @foreach($value as $header_name => $header_value)
                                                            @if($leadHeaderRow->slug == $header_name)
                                                            value="{{$header_value}}"
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    type="tel" 
                                                    minlength="10" 
                                                    maxlength="10" 
                                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10);" 
                                                    onblur="if(this.value!=='' && this.value.length!==10){ 
                                                        alert('Please enter a valid phone number !'); 
                                                        this.value=''; 
                                                        this.focus(); 
                                                    }" 
                                                    class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif placeholder="Enter {{$leadHeaderRow->name}}">
                                                </div> 
                                            @elseif($leadHeaderRow->slug == 'whatsapp-number')                                                   
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                                    <input
                                                    @foreach($row as $key => $value)
                                                        @foreach($value as $header_name => $header_value)
                                                            @if($leadHeaderRow->slug == $header_name)
                                                            value="{{$header_value}}"
                                                            @endif
                                                        @endforeach
                                                    @endforeach 
                                                    type="tel" 
                                                    minlength="10" 
                                                    maxlength="10" 
                                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10);" 
                                                    onblur="if(this.value!=='' && this.value.length!==10){ 
                                                        alert('Please enter a valid whatsapp number !'); 
                                                        this.value=''; 
                                                        this.focus(); 
                                                    }" 
                                                    class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif placeholder="Enter {{$leadHeaderRow->name}}">
                                                </div> 
                                            @elseif($leadHeaderRow->slug == 'email')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                                    <input
                                                    @foreach($row as $key => $value)
                                                        @foreach($value as $header_name => $header_value)
                                                            @if($leadHeaderRow->slug == $header_name)
                                                            value="{{$header_value}}"
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    type="email"
                                                    oninput="this.value = this.value.toLowerCase();" 
                                                    onblur="if(this.value!=='' && !/^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(this.value)){ 
                                                        alert('Please enter a valid email address !'); 
                                                        this.value=''; 
                                                        this.focus(); 
                                                    }"
                                                    class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif placeholder="Enter {{$leadHeaderRow->name}}">
                                                </div>
                                            @else
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                                    <input
                                                    @foreach($row as $key => $value)
                                                        @foreach($value as $header_name => $header_value)
                                                            @if($leadHeaderRow->slug == $header_name)
                                                            value="{{$header_value}}"
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    type="text" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif placeholder="Enter {{$leadHeaderRow->name}}">
                                                    
                                                </div>                                                
                                            @endif                                                              
                                        @endif

                                        
                                        
                                        @if($leadHeaderRow->input_type == 'TEXTAREA')
                                        <div class="col-md-6 mb-3">
                                            <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                            @foreach($row as $key => $value)
                                                @foreach($value as $header_name => $header_value)
                                                    @if($leadHeaderRow->slug == $header_name)
                                                        @php
                                                            $textareaValue = $header_value
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            <textarea class="form-control" rows="1" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif placeholder="Enter {{$leadHeaderRow->name}}">{{$textareaValue}}</textarea>                                           
                                        </div>
                                        @endif
                                        
                                        @if($leadHeaderRow->input_type == 'DROPDOWN')
                                        
                                            @if($leadHeaderRow->slug == 'country')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                                    <input type="hidden" name="{{$leadHeaderRow->slug}}" value="">
                                                    <select class="select2 form-select" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif>
                                                        
                                                        @foreach($row as $key => $value)
                                                            @foreach($value as $header_name => $header_value)
                                                                @if($leadHeaderRow->slug == $header_name)
                                                                    @if(!empty($header_value))
                                                                        <option value="" disabled>Select {{$leadHeaderRow->name}}</option>
                                                                    @else
                                                                        <option value="" selected disabled>Select {{$leadHeaderRow->name}}</option>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        
                                                        @foreach($country as $countryRow)
                                                            <option value="{{$countryRow->name}}" data-countryid="{{$countryRow->id}}" 
                                                                @foreach($row as $key => $value)
                                                                    @foreach($value as $header_name => $header_value)
                                                                        @if($leadHeaderRow->slug == $header_name)
                                                                            @if($countryRow->name == $header_value)
                                                                             selected
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                            >{{$countryRow->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            @if($leadHeaderRow->slug == 'phone-code')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                                    <input type="hidden" name="{{$leadHeaderRow->slug}}" value="">
                                                    <select class="select2 form-select" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif>
                                                        
                                                        @foreach($row as $key => $value)
                                                            @foreach($value as $header_name => $header_value)
                                                                @if($leadHeaderRow->slug == $header_name)
                                                                    @if(!empty($header_value))
                                                                        <option value="" disabled>Select {{$leadHeaderRow->name}}</option>
                                                                    @else
                                                                        <option value="" selected disabled>Select {{$leadHeaderRow->name}}</option>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endforeach

                                                        @foreach($country as $countryRow)
                                                            <option value="{{$countryRow->phone_code}}"
                                                                @foreach($row as $key => $value)
                                                                    @foreach($value as $header_name => $header_value)
                                                                        @if($leadHeaderRow->slug == $header_name)
                                                                            @if($countryRow->phone_code == $header_value)
                                                                             selected
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                            >{{$countryRow->phone_code}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            
                                            @if($leadHeaderRow->slug == 'state')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                                    <input type="hidden" name="{{$leadHeaderRow->slug}}" value="">
                                                    <select class="select2 form-select" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif>
                                                       
                                                        @foreach($row as $key => $value)
                                                            @foreach($value as $header_name => $header_value)
                                                                @if($leadHeaderRow->slug == $header_name)
                                                                    @if(!empty($header_value))
                                                                        <option value="" disabled>Select {{$leadHeaderRow->name}}</option>
                                                                    @else
                                                                        <option value="" selected disabled>Select {{$leadHeaderRow->name}}</option>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endforeach

                                                        @foreach($state as $stateRow)
                                                            <option value="{{$stateRow->name}}"
                                                                @foreach($row as $key => $value)
                                                                    @foreach($value as $header_name => $header_value)
                                                                        @if($leadHeaderRow->slug == $header_name)
                                                                            @if($stateRow->name == $header_value)
                                                                             selected
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                            >{{$stateRow->name}}</option>
                                                        @endforeach
                                                </select>
                                                </div>
                                            @endif

                                            @if($leadHeaderRow->slug == 'source')
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                                    <input type="hidden" name="{{$leadHeaderRow->slug}}" value="">
                                                    <select class="select2 form-select" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif>
                                                        
                                                        @foreach($row as $key => $value)
                                                            @foreach($value as $header_name => $header_value)
                                                                @if($leadHeaderRow->slug == $header_name)
                                                                    @if(!empty($header_value))
                                                                        <option value="" disabled>Select {{$leadHeaderRow->name}}</option>
                                                                    @else
                                                                        <option value="" selected disabled>Select {{$leadHeaderRow->name}}</option>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endforeach

                                                        @foreach($source as $sourceRow)
                                                            <option value="{{$sourceRow->name}}"
                                                                @foreach($row as $key => $value)
                                                                    @foreach($value as $header_name => $header_value)
                                                                        @if($leadHeaderRow->slug == $header_name)
                                                                            @if($sourceRow->name == $header_value)
                                                                             selected
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                            >{{$sourceRow->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            
                                        @endif
                                        
                                        {{-- @if($leadHeaderRow->input_type == 'CHECKBOX')
                                        <div class="col-md-6 mb-3">
                                            <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                            <input type="checkbox" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif>
                                        </div>
                                        @endif
                                        
                                        @if($leadHeaderRow->input_type == 'RADIO')
                                        <div class="col-md-6 mb-3">
                                            <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                            <input type="radio" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif>
                                        </div>
                                        @endif --}}
                                        
                                        @if($leadHeaderRow->input_type == 'DATE')
                                        <div class="col-md-6 mb-3">
                                            <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                            @foreach($row as $key => $value)
                                                @foreach($value as $header_name => $header_value)
                                                   @if(!empty($header_value))

                                                        @if($leadHeaderRow->slug == $header_name)
                                                            @php
                                                                try {
                                                                    $parsed = Carbon::createFromFormat('d-m-Y', $header_value);
                                                                } catch (\Exception $e) {
                                                                    try {
                                                                        $parsed = Carbon::parse($header_value);
                                                                    } catch (\Exception $e) {
                                                                        $parsed = null;
                                                                    }
                                                                }
                                                                if ($parsed) {
                                                                    $dateValue = $parsed->format('Y-m-d');
                                                                }
                                                            @endphp
                                                        @endif

                                                    @else 
                                                        @php
                                                        $dateValue = '';  
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            <input value="{{$dateValue}}" type="date" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif>
                                        </div>
                                        @endif
                                        
                                        @if($leadHeaderRow->input_type == 'TIME')
                                        <div class="col-md-6 mb-3">
                                            <label for="{{$leadHeaderRow->slug}}" class="form-label">{{$leadHeaderRow->name}} @if(in_array($leadHeaderRow->slug, $isRequiredArr))<small class="text-danger">*</small> @endif</label>
                                            @foreach($row as $key => $value)
                                                @foreach($value as $header_name => $header_value)
                                                    @if(!empty($header_value))

                                                        @if($leadHeaderRow->slug == $header_name)
                                                            @php
                                                                try {
                                                                    $parsedTime = Carbon::createFromFormat('h:i A', $header_value);
                                                                } catch (\Exception $e) {
                                                                    try {
                                                                        $parsedTime = Carbon::parse($header_value);
                                                                    } catch (\Exception $e) {
                                                                        $parsedTime = null;
                                                                    }
                                                                }
                                                                if ($parsedTime) {
                                                                    $timeValue = $parsedTime->format('H:i'); // format as 24-hour time
                                                                }
                                                            @endphp
                                                        @endif
                                                        
                                                    @else
                                                        @php
                                                            $timeValue = '';
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            <input
                                            type="time" value="{{$timeValue}}" class="form-control" id="{{$leadHeaderRow->slug}}" name="{{$leadHeaderRow->slug}}" @if(in_array($leadHeaderRow->slug, $isRequiredArr)) required @endif>
                                        </div>
                                        @endif

                                    @endforeach
                               

                            </div>


                            <div class="mt-4">
                                <button type="submit" class="btn btn-outline-dark btn-sm me-2">Save Changes</button>
                                <a href="<?= url($controllerRoute) ?>" class="btn btn-outline-danger btn-sm">Cancel</a>
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
