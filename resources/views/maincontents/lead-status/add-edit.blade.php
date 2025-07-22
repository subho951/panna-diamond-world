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
                $parent_id                  = $row->parent_id;
                $name                       = $row->name;
                $rank                       = $row->rank;
                $short_description          = $row->short_description;
                $background_color           = $row->background_color;
                $font_color                 = $row->font_color;
                $is_registered              = $row->is_registered;
                $status                     = $row->status;
            } else {
                $parent_id                  = '';
                $name                       = '';
                $rank                       = '';
                $short_description          = '';
                $background_color           = '';
                $font_color                 = '';
                $is_registered              = '';
                $status                     = '';
            }
            ?>
            <div class="card-body">
                <form id="formAccountSettings" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">Parent Status <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="parent_id" name="parent_id">
                                <option value="" selected>Select Parent Status</option>
                                <?php if($parent_Stats){ foreach($parent_Stats as $parent_Stat){?>
                                    <option value="<?=$parent_Stat->id?>" <?=(($parent_Stat->id == $parent_id)?'selected':'')?>><?=$parent_Stat->name?></option>
                                <?php } }?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="name" name="name" value="<?=$name?>" required placeholder="Name" autofocus />
                        </div>
                        
                        <div class="col-md-6 mt-3">
                            <label for="rank" class="form-label">Rank <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="rank" name="rank" required>
                                <option value="" selected>Select Rank</option>
                                <?php for($y=1; $y<=20; $y++){?>
                                    <option value="<?=$y?>" <?=(($y == $rank)?'selected':'')?>><?=$y?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="short_description" class="form-label">Short Description <small class="text-danger">*</small></label>
                            <textarea class="form-control" id="short_description" name="short_description" required placeholder="Short Description" rows="1" maxlength="100"><?=$short_description?></textarea>
                            <small class="text-danger">Max 100 characters allowed</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="background_color" class="form-label">Background Color <small class="text-danger">*</small></label>
                            <input class="form-control" type="color" id="background_color" name="background_color" value="<?=$background_color?>" required placeholder="Background Color" />
                        </div>
                        <div class="col-md-6">
                            <label for="font_color" class="form-label">Font Color <small class="text-danger">*</small></label>
                            <input class="form-control" type="color" id="font_color" name="font_color" value="<?=$font_color?>" required placeholder="Font Color" />
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="is_registered" class="form-label d-block">Is Registered <small class="text-danger">*</small></label>
                            <div class="form-check form-switch mt-0 ">
                                <input class="form-check-input" type="checkbox" name="is_registered" role="switch" id="is_registered" <?=(($is_registered == 'YES')?'checked':'')?>>
                                <label class="form-check-label" for="is_registered">YES</label>
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