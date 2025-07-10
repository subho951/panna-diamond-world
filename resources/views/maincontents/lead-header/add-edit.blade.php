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
                $name                       = $row->name;
                $input_type                 = $row->input_type;
                $rank                       = $row->rank;
                $is_visible_in_lead_list    = $row->is_visible_in_lead_list;
                $status                     = $row->status;
            } else {
                $name                       = '';
                $input_type                 = '';
                $rank                       = '';
                $is_visible_in_lead_list    = '';
                $status                     = '';
            }
            ?>
            <div class="card-body">
                <form id="formAccountSettings" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <label for="name" class="form-label">Name <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="name" name="name" value="<?=$name?>" required placeholder="Name" autofocus />
                        </div>
                        <div class="col-md-4">
                            <label for="input_type" class="form-label">Input Type <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="input_type" name="input_type" required>
                                <option value="" selected>Select Input Type</option>
                                <option value="TEXTBOX" <?=(($input_type == 'TEXTBOX')?'selected':'')?>>TEXTBOX</option>
                                <option value="TEXTAREA" <?=(($input_type == 'TEXTAREA')?'selected':'')?>>TEXTAREA</option>
                                <option value="DROPDOWN" <?=(($input_type == 'DROPDOWN')?'selected':'')?>>DROPDOWN</option>
                                <option value="CHECKBOX" <?=(($input_type == 'CHECKBOX')?'selected':'')?>>CHECKBOX</option>
                                <option value="RADIO" <?=(($input_type == 'RADIO')?'selected':'')?>>RADIO</option>
                                <option value="DATE" <?=(($input_type == 'DATE')?'selected':'')?>>DATE</option>
                                <option value="TIME" <?=(($input_type == 'TIME')?'selected':'')?>>TIME</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="rank" class="form-label">Rank <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="rank" name="rank" required>
                                <option value="" selected>Select Rank</option>
                                <?php for($y=1; $y<=20; $y++){?>
                                    <option value="<?=$y?>" <?=(($y == $rank)?'selected':'')?>><?=$y?></option>
                                <?php }?>
                            </select>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="is_visible_in_lead_list" class="form-label d-block">Is Visible In Lead List <small class="text-danger">*</small></label>
                            <div class="form-check form-switch mt-0 ">
                                <input class="form-check-input" type="checkbox" name="is_visible_in_lead_list" role="switch" id="is_visible_in_lead_list" <?=(($is_visible_in_lead_list == 'YES')?'checked':'')?>>
                                <label class="form-check-label" for="is_visible_in_lead_list">YES</label>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
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
@endsection