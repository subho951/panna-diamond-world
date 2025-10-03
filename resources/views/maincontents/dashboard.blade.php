
<?php
use App\Helpers\Helper;
?>
@extends('layouts.main')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
  <div class="row g-6">
    <div class="col-lg-12">
      <h3 class="mt-2 main_heading">Welcome to <?=Helper::getSettingValue('site_name')?></h2>
    </div>
     
    <div class="row">
      <div class="col-md-4 mb-3">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Total Contacts</h5>
            <p class="card-text">{{ $noOfUniqueLeads }}</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Branches</h5>
            <p class="card-text">{{ $noOfBranches }}</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Telecallers</h5>
            <p class="card-text">{{ $noOfTelecallers }}</p>
          </div>
        </div>
      </div>
    </div>


    

    
    {{-- <!-- Average Daily Sales -->
    <div class="col-xxl-2 col-xl-2 col-md-6 col-sm-6">
      <div class="card h-100">
        <div class="card-header pb-3">
          <h5 class="card-title mb-1">Order</h5>
          <p class="card-subtitle">Last week</p>
        </div>
        <div class="card-body">
          <div id="ordersLastWeek"></div>
          <div class="d-flex justify-content-between align-items-center gap-3">
            <h4 class="mb-0">124k</h4>
            <small class="text-success">+12.6%</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Average Daily Sales -->
    <div class="col-xxl-2 col-xl-2 col-md-6 col-sm-6 mt-3 mt-sm-0">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h5 class="card-title mb-1">Sales</h5>
          <p class="card-subtitle">Last Year</p>
        </div>
        <div id="salesLastYear"></div>
        <div class="card-body pt-0">
          <div class="d-flex justify-content-between align-items-center mt-3 gap-3">
            <h4 class="mb-0">175k</h4>
            <small class="text-danger">-16.2%</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Projects table -->
    <div class="col-xxl-8 col-xl-8 col-md-8 col-sm-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title m-0">
            <h5 class="mb-1">Earning Reports</h5>
            <p class="card-subtitle">Yearly Earnings Overview</p>
          </div>
          <div class="dropdown">
            <button
              class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1"
              type="button"
              id="earningReportsTabsId"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false">
              <i class="ti ti-dots-vertical ti-md text-muted"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsTabsId">
              <a class="dropdown-item" href="javascript:void(0);">View More</a>
              <a class="dropdown-item" href="javascript:void(0);">Delete</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="nav nav-tabs widget-nav-tabs pb-8 gap-4 mx-1 d-flex flex-nowrap" role="tablist">
            <li class="nav-item">
              <a
                href="javascript:void(0);"
                class="nav-link btn active d-flex flex-column align-items-center justify-content-center"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs-orders-id"
                aria-controls="navs-orders-id"
                aria-selected="true">
                <div class="badge bg-label-secondary rounded p-2">
                  <i class="fa fa-shopping-cart fa-md"></i>
                </div>
                <h6 class="tab-widget-title mb-0 mt-2">Orders</h6>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="javascript:void(0);"
                class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs-sales-id"
                aria-controls="navs-sales-id"
                aria-selected="false">
                <div class="badge bg-label-secondary rounded p-2">
                  <i class="fa fa-chart-bar fa-md"></i>
                </div>
                <h6 class="tab-widget-title mb-0 mt-2">Sales</h6>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="javascript:void(0);"
                class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs-profit-id"
                aria-controls="navs-profit-id"
                aria-selected="false">
                <div class="badge bg-label-secondary rounded p-2">
                  <i class="fa fa-rupee-sign fa-md"></i>
                </div>
                <h6 class="tab-widget-title mb-0 mt-2">Profit</h6>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="javascript:void(0);"
                class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                role="tab"
                data-bs-toggle="tab"
                data-bs-target="#navs-income-id"
                aria-controls="navs-income-id"
                aria-selected="false">
                <div class="badge bg-label-secondary rounded p-2">
                  <i class="fas fa-chart-pie fa-md"></i>
                </div>
                <h6 class="tab-widget-title mb-0 mt-2">Income</h6>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="javascript:void(0);"
                class="nav-link btn d-flex align-items-center justify-content-center disabled"
                role="tab"
                data-bs-toggle="tab"
                aria-selected="false">
                <div class="badge bg-label-secondary rounded p-2"><i class="ti ti-plus ti-md"></i></div>
              </a>
            </li>
          </ul>
          <div class="tab-content p-0 ms-0 ms-sm-2">
            <div class="tab-pane fade show active" id="navs-orders-id" role="tabpanel">
              <div id="earningReportsTabsOrders"></div>
            </div>
            <div class="tab-pane fade" id="navs-sales-id" role="tabpanel">
              <div id="earningReportsTabsSales"></div>
            </div>
            <div class="tab-pane fade" id="navs-profit-id" role="tabpanel">
              <div id="earningReportsTabsProfit"></div>
            </div>
            <div class="tab-pane fade" id="navs-income-id" role="tabpanel">
              <div id="earningReportsTabsIncome"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Projects table --> --}}
  </div>
</div>
<!-- / Content -->
@endsection
@section('scripts')
<!-- Page JS -->
<script src="<?=config('constants.admin_assets_url')?>assets/js/dashboards-crm.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
<script>
  function toastAlert(type, message, redirectStatus = false, redirectUrl = ''){
    toastr.options = {
        "closeButton": true,
        "debug": true,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-bottom-left",
        "preventDuplicates": false,
        "showDuration": "3000",
        "hideDuration": "1000000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    toastr[type](message);
    if(redirectStatus){        
        setTimeout(function(){ window.location = redirectUrl; }, 3000);
    }
  }
  // toastAlert('success', 'suceess message');
  // toastAlert('error', 'error message');
  // toastAlert('warning', 'warning message');
  // toastAlert('info', 'info message');
</script>
@endsection