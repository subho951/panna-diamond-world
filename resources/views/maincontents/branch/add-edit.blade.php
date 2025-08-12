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
                $name               = $row->name;
                $prefix             = $row->prefix;
                $email              = $row->email;
                $phone_code         = $row->phone_code;
                $phone              = $row->phone;
                $address            = $row->address;
                $pincode            = $row->pincode;
                $status             = $row->status;
            } else {
                $name               = '';
                $prefix             = '';
                $email              = '';
                $phone_code         = '';
                $phone              = '';
                $address            = '';
                $pincode            = '';
                $status             = '';
            }
            ?>
            <div class="card-body">
                <form id="formAccountSettings" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="name" name="name" value="<?=$name?>" autofocus required placeholder="Name" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prefix" class="form-label">Prefix <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="prefix" name="prefix" value="<?=$prefix?>" required placeholder="Prefix" 
                            minlength="3" maxlength="5"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="email" name="email" value="<?=$email?>" required placeholder="Email"
                            oninput="this.value = this.value.toLowerCase();" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone_code" class="form-label">Country Code <small class="text-danger">*</small></label>
                            <select class="select2 form-select" type="text" id="phone_code" name="phone_code" required>
                                <option value="" selected disabled>Select Country Code</option>
                                <?php if($country){ foreach($country as $countryRow){?>
                                    <option value="<?=$countryRow->phone_code?>" <?=(($countryRow->phone_code === $phone_code)? 'selected':'')?>><?=$countryRow->phone_code?></option>
                                <?php } }?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="phone" name="phone" value="<?=$phone?>" required placeholder="Phone" minlength="10" maxlength="10"/>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address <small class="text-danger">*</small></label>
                            <textarea class="form-control" id="address" name="address" required placeholder="Address" rows="1"><?= $address ?></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="pincode" class="form-label">Pincode <small class="text-danger">*</small></label>
                            <input class="form-control" type="text" id="pincode" name="pincode" value="<?=$pincode?>" required placeholder="Pincode" />
                        </div>

                        <div class="col-md-6 mb-3">
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