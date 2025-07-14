
<?php
use App\Helpers\Helper;
?>
@extends('layouts.main')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-6">
    <div class="col-lg-12">
      <h3 class="mt-2 main_heading">Welcome to <?=Helper::getSettingValue('site_name')?> masteradmin panel</h2>
    </div>

    <!-- Average Daily Sales -->
    <div class="col-xxl-2 col-xl-2 col-md-6 col-sm-6">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h5 class="mb-3 card-title">Total Data</h5>
          <!-- <p class="mb-0 text-body">Total Sales This Month</p> -->
          <h4 class="mb-0">0</h4>
        </div>
      </div>
    </div>
    <!--/ Average Daily Sales -->

    <!-- Average Daily Sales -->
    <div class="col-xxl-2 col-xl-2 col-md-6 col-sm-6 mt-3 mt-sm-0">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h5 class="mb-3 card-title">Total Data</h5>
          <!-- <p class="mb-0 text-body">Total Sales This Month</p> -->
          <h4 class="mb-0">0</h4>
        </div>
      </div>
    </div>
    <!--/ Average Daily Sales -->

    <!-- Projects table -->
    {{-- <div class="col-xxl-8 col-xl-8 col-md-8 col-sm-8">
      <div class="card">
        <div class="card-datatable table-responsive">
          
        </div>
      </div>
    </div> --}}
    <!--/ Projects table -->
  </div>
</div>
<!-- / Content -->
 @endsection

