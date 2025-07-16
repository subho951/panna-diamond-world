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
         <span class="text-muted fw-light"><a href="<?=url('dashboard')?>">Dashboard</a> /</span> <?=$page_header?>
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
            <div class="card-header pb-2">
                <a href="<?=url($controllerRoute . '/add/')?>" class="btn btn-outline-success btn-sm float-end">Add <?=$module['title']?></a>
            </div>
            <div class="card-body">
               <div id="table-overlay-loader" class="text-loader">
                  Fetching data. Please wait <span id="dot-animation">.</span>
               </div>
                @include('components.table', [
                'containerId' => 'table1',
                'searchId' => 'search1',
                'table' => 'lead_statuses',
                'columns' => ['parent_id', 'name', 'rank', 'short_description', 'is_registered', 'created_at', 'status'],
                'visibleColumns' => ['parent_name', 'name', 'rank', 'short_description', 'is_registered', 'created_at'],    // used for rendering
                'headers' => ['#', 'Parent Status', 'Name', 'Rank', 'Short Description', 'Is Registered', 'Created At'],
                'filename' => "Lead_Status",
                'orderBy' => 'id',
                'orderType' => 'desc',
                'conditions' => [
                    ['column' => 'status', 'operator' => '!=', 'value' => 3]
                ],
                'routePrefix' => 'lead-status',
                'showActions' => true, // set to false to hide actions
                'statusColumn' => 'status' // optional, defaults to 'is_active'
                ])
            </div>
        </div>
      </div>
   </div>
</div>
@endsection
@section('scripts')
<script src="<?=config('constants.admin_assets_url')?>assets/js/table.js"></script>
@endsection