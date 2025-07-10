<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row g-6">
      <h4><?=$page_header?></h4>
      <h6 class="breadcrumb-wrapper">
            <span class="text-muted fw-light"><a href="<?=url('dashboard')?>">Dashboard</a> /</span>
            <span class="text-muted fw-light"><a href="<?=url($controllerRoute . '/list/')?>"><?=$module['title']?> List</a> /</span>
            <?=$page_header?>
      </h6>
      <div class="nav-align-top mb-4">
         <?php if(session('success_message')){?>
            <div class="alert alert-success alert-dismissible autohide" role="alert">
               <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-desktop align-top me-2"></i>Success!</h6>
               <span><?=session('success_message')?></span>
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
               </button>
            </div>
         <?php }?>
         <?php if(session('error_message')){?>
            <div class="alert alert-danger alert-dismissible autohide" role="alert">
               <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-store align-top me-2"></i>Error!</h6>
               <span><?=session('error_message')?></span>
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
               </button>
            </div>
         <?php }?>
         <div class="card mb-4">
            <?php
            if($row){
                $id                 = $row->id;
                $country_id         = $row->country_id;
                $state_id           = $row->state_id;
                $name               = $row->name;
                $status             = $row->status;
            } else {
                $id                 = '';
                $country_id         = '';
                $state_id           = '';
                $name               = '';
                $status             = '';
            }
            ?>
            <div class="card-body">
                <form id="formAccountSettings" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="country_id" class="form-label">Country <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="country_id" name="country_id" required>
                                <option value="" selected>Select Country</option>
                                <?php if($couns){ foreach($couns as $coun){?>
                                    <option value="<?=$coun->id?>" <?=(($coun->id == $country_id)?'selected':'')?>><?=$coun->name?></option>
                                <?php } }?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state_id" class="form-label">State <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="state_id" name="state_id" required>
                                @if(isset($sts) && count($sts) > 0)
                                    @foreach($sts as $state)
                                        <option value="{{ $state->id }}" {{ (old('state_id', $state_id ?? '') == $state->id) ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Select State</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">Name <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="name" name="name" value="<?=$name?>" required placeholder="Name" autofocus />
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label d-block">Status <small class="text-danger">*</small></label>
                            <div class="form-check form-switch mt-0 ">
                                <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" <?=(($status == 1)?'checked':'')?>>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary btn-sm me-2">Save Changes</button>
                        <a href="<?=url($controllerRoute . '/list/')?>" class="btn btn-label-secondary btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
      </div>
   </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle country change
        $('#country_id').on('change', function() {
            let countryId = $(this).val();
            if (countryId) {
                var base_url = '<?=url('/')?>';
                $.ajax({
                    url: base_url + '/states/' + countryId,
                    type: 'GET',
                    success: function(states) {
                        let $state = $('#state_id');
                        $state.empty();
                        $state.append('<option value="">Select State</option>');
                        $.each(states, function(key, value) {
                            $state.append('<option value="'+ key +'">'+ value +'</option>');
                        });
                    }
                });
            } else {
                $('#state_id').empty();
                $('#state_id').append('<option value="">Select State</option>');
            }
        });

        // Auto-trigger country change on page load if editing
        @if(isset($city))
            $('#country_id').trigger('change');

            // Wait for AJAX to load, then set selected state
            let selectedStateId = '{{ $city->state_id }}';
            $(document).ajaxStop(function() {
                $('#state_id').val(selectedStateId);
            });
        @endif
    });
</script>
@endsection