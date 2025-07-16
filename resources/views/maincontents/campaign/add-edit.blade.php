<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
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
                $campaign_type_id   = $row->campaign_type_id;
                $name               = $row->name;
                $year               = $row->year;
                $status             = $row->status;
            } else {
                $id                 = '';
                $campaign_type_id   = '';
                $name               = '';
                $year               = '';
                $status             = '';
            }
            ?>
            <div class="card-body">
                <form id="formAccountSettings" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="campaign_type_id" class="form-label">Campaign Type <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="campaign_type_id" name="campaign_type_id" required>
                                <option value="" selected>Select Campaign Type</option>
                                <?php if($camTypes){ foreach($camTypes as $camType){?>
                                    <option value="<?=$camType->id?>" <?=(($camType->id == $campaign_type_id)?'selected':'')?>><?=$camType->name?></option>
                                <?php } }?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="name" name="name" value="<?=$name?>" required placeholder="Name" autofocus />
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="year" class="form-label">Year <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="year" name="year" required>
                                <option value="" selected>Select Year</option>
                                <?php for($y=date('Y'); $y<=2100; $y++){?>
                                    <option value="<?=$y?>" <?=(($y == $year)?'selected':'')?>><?=$y?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="status" class="form-label d-block">Status <small class="text-danger">*</small></label>
                            <div class="form-check form-switch mt-0 ">
                                <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" <?=(($status == 1)?'checked':'')?>>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-outline-dark btn-sm me-2">Save Changes</button>
                        <a href="<?=url($controllerRoute . '/list/')?>" class="btn btn-outline-danger btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
      </div>
   </div>
</div>
@endsection