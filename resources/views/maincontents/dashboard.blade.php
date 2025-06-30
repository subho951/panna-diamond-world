
<?php
use App\Helpers\Helper;
?>
@extends('layouts.main')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-6">
    <div class="col-lg-12">
      <h2>Welcome to <?=Helper::getSettingValue('site_name')?> masteradmin panel</h2>
    </div>

    <!-- Average Daily Sales -->
    <div class="col-xxl-2 col-xl-2 col-md-2 col-sm-2">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h5 class="mb-3 card-title">Total Companies</h5>
          <!-- <p class="mb-0 text-body">Total Sales This Month</p> -->
          <h4 class="mb-0"><?=$company_count?></h4>
        </div>
      </div>
    </div>
    <!--/ Average Daily Sales -->

    <!-- Average Daily Sales -->
    <div class="col-xxl-2 col-xl-2 col-md-2 col-sm-2">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h5 class="mb-3 card-title">Total Industries</h5>
          <!-- <p class="mb-0 text-body">Total Sales This Month</p> -->
          <h4 class="mb-0"><?=$industry_count?></h4>
        </div>
      </div>
    </div>
    <!--/ Average Daily Sales -->

    <!-- Projects table -->
    <div class="col-xxl-8 col-xl-8 col-md-8 col-sm-8">
      <div class="card">
        <div class="card-datatable table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email<br>Phone</th>
                <th>Package</th>
                <th>Start<br>End</th>
                <th>Team</th>
                <th class="w-px-200">Licence No.</th>
              </tr>
            </thead>
            <tbody>
              <?php if($companies){ $sl=1; foreach($companies as $company){?>
                <tr>
                  <td><?=$sl++?></td>
                  <td>
                    <h6><?=$company->name?></h6>
                    <small style="font-size: 10px;"><?=$company->industry_name?></small>
                  </td>
                  <td>
                    <span><?=$company->email?></span><br>
                    <span><?=$company->phone?></span>
                  </td>
                  <td><?=$company->package_name?></td>
                  <td>
                    <span><?=date_format(date_create($company->start_date), "M d, Y")?></span><br>
                    <span><?=date_format(date_create($company->end_date), "M d, Y")?></span>
                  </td>
                  <td><?=$company->no_of_employee?></td>
                  <td><?=$company->licence_no?></td>
                </tr>
              <?php } }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--/ Projects table -->
  </div>
</div>
<!-- / Content -->
 @endsection

