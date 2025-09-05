
<?php
use App\Helpers\Helper;
?>
@extends('layouts.main')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
  <div class="row g-6">
    <div class="col-lg-12">
      <h3 class="mt-2 main_heading">Welcome to <?=Helper::getSettingValue('site_name')?> masteradmin panel</h2>
    </div>
    {{-- filter section start --}}
    <div class="card mb-3 p-3" >
      <form>
          @csrf
          <div class="row">
              <div class="col-md-9">
                  <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="assigned_from_date" class="form-label">Assigned From </label>
                        <input class="form-control" type="date" id="assigned_from_date" name="assigned_from_date"
                          @if(!empty($assignedFromDate))
                            value="{{ $assignedFromDate }}" 
                          @endif
                          max="<?=date('Y-m-d')?>" />
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="assigned_to_date" class="form-label">Assigned To </label>
                        <input class="form-control" type="date" id="assigned_to_date" name="assigned_to_date"
                          @if(!empty($assignedToDate))
                            value="{{ $assignedToDate }}"
                          @endif
                          max="<?=date('Y-m-d')?>" />
                      </div>
                  </div>
              </div>
              <div class="col-md-3 mt-3">
                  <div class="row">
                      <div class="col-md-12 d-flex gap-2 mt-3">
                          <button type="button" class="w-100 btn btn-outline-dark filterBtn">
                              <i class="fa-solid fa-filter"></i>&nbsp;<span>Filter</span>
                          </button>
                          <button type="button" class="w-100 btn btn-label-secondary d-none resetBtn">
                              <i class="fa-solid fa-arrow-rotate-left"></i>&nbsp;<span>Reset</span>
                          </button>
                      </div>
                  </div>
              </div>
          </div>                                       
      </form>
    </div>
    {{-- filter section end --}}
   

    @if(session('user_data')['role_id'] == 3) {{-- for telecaller --}}

      @foreach($branchWiseTelecallerActivity as $eachBranchWiseTelecallerActivity)
        @if(!empty($eachBranchWiseTelecallerActivity["telecallerActivity"]))
          <div class="card mb-3 p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                  <div>
                      <span class="card-header fw-bold h6 ps-0">{{ $eachBranchWiseTelecallerActivity["branch_name"] }}</span> 
                  </div>
              </div>
              <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                      <thead>
                          <tr>
                              <th>Telecaller | Last Call</th>
                              <th class="text-center">Total Calls</th>
                              <th class="text-center">Pending</th>
                              <th class="text-center">Follow Up</th>
                              <th class="text-center">Success</th>
                              <th class="text-center">Dump</th>
                          </tr>
                      </thead>
                      <tbody class="table-border-bottom-0">
                        @if(!empty($eachBranchWiseTelecallerActivity["telecallerActivity"]))
                            {{-- @dd($eachBranchWiseTelecallerActivity["telecallerActivity"]); --}}
                            @foreach($eachBranchWiseTelecallerActivity["telecallerActivity"] as $key => $value)
                              {{-- @dd($value); --}}
                              <tr>
                                  <td>
                                    @if(!empty($value["telecaller_name"]))
                                      <span class="badge badge-center rounded-pill bg-label-danger">
                                        <i class="fa-solid fa-user-tie"></i>
                                      </span>
                                      <strong>{{ $value["telecaller_name"] }} </strong>
                                      <br>
                                    @endif
                                    @if(!empty($value["last_call_of_telecaller"]))
                                      <span class="badge badge-center rounded-pill bg-label-warning mt-1">
                                        <i class="fa-solid fa-headset"></i>
                                      </span>
                                      <strong class="text-muted">{{ $value["last_call_of_telecaller"]}}</strong>
                                    @endif
                                  </td>
                                  <td class="text-center">{{ $value["total_call_count"] }}</td>
                                  <td class="text-center">{{ $value["parentStatus_new_count"] }}</td>
                                  <td class="text-center">{{ $value["parentStatus_followUp_count"] }}</td>
                                  <td class="text-center">{{ $value["parentStatus_success_count"] }}</td>
                                  <td class="text-center">{{ $value["parentStatus_dumb_count"] }}</td>
                              </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center text-danger">No Telecallers Found</td>
                        </tr>
                        @endif
                      </tbody>
                  </table>
              </div>
          </div>
        @endif
      @endforeach

    @else {{-- for admin --}}

      @foreach($branchWiseTelecallerActivity as $eachBranchWiseTelecallerActivity)
      {{-- @dd($eachBranchWiseTelecallerActivity); --}}
        
        <div class="card mb-3 p-3 branchWiseTelecallerAcitivity">
          <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="d-flex align-items-center justify-content-between w-100">
                  <span class="branchName card-header fw-bold h6 p-0">{{ $eachBranchWiseTelecallerActivity["branch_name"] }}</span>
                  <button class="exportAsCSV btn btn-sm"
                      style="border: 1px solid green; background-color: green; color: #FFF;">
                      <i class="fa-solid fa-file-csv"></i>&nbsp;Export CSV
                  </button>
              </div>
          </div>
          <div class="table-responsive text-nowrap">
              <table class="table table-striped">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>Telecaller | Last Call</th>
                          <th class="text-center">Total Calls</th>
                          <th class="text-center">Pending</th>
                          <th class="text-center">Follow Up</th>
                          <th class="text-center">Success</th>
                          <th class="text-center">Dump</th>
                      </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                    @if(!empty($eachBranchWiseTelecallerActivity["telecallerActivity"]))

                        @php
                          $SUM_total_call_count             = 0 ; 
                          $SUM_parentStatus_new_count       = 0 ;
                          $SUM_parentStatus_followUp_count  = 0 ;
                          $SUM_parentStatus_success_count   = 0 ;
                          $SUM_parentStatus_dumb_count      = 0 ;
                        @endphp

                        @foreach($eachBranchWiseTelecallerActivity["telecallerActivity"] as $key => $value)
                          {{-- @dd($value); --}}
                          @php
                            $SUM_total_call_count             = $SUM_total_call_count            + $value["total_call_count"];
                            $SUM_parentStatus_new_count       = $SUM_parentStatus_new_count      + $value["parentStatus_new_count"];
                            $SUM_parentStatus_followUp_count  = $SUM_parentStatus_followUp_count + $value["parentStatus_followUp_count"];
                            $SUM_parentStatus_success_count   = $SUM_parentStatus_success_count  + $value["parentStatus_success_count"];
                            $SUM_parentStatus_dumb_count      = $SUM_parentStatus_dumb_count     + $value["parentStatus_dumb_count"];    
                          @endphp

                          <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td class="telecaller_lastcall">
                                @if(!empty($value["telecaller_name"]))
                                  <span class="badge badge-center rounded-pill bg-label-danger">
                                    <i class="fa-solid fa-user-tie"></i>
                                  </span>
                                  <strong>{{ $value["telecaller_name"] }} </strong><br>
                                @endif
                                @if(!empty($value["last_call_of_telecaller"]))
                                  <span class="badge badge-center rounded-pill bg-label-warning mt-1">
                                    <i class="fa-solid fa-headset"></i>
                                  </span>
                                  <strong class="text-muted">{{ $value["last_call_of_telecaller"]}}</strong>
                                @endif
                              </td>
                              <td class="text-center">{{ $value["total_call_count"] }}</td>
                              <td class="text-center">{{ $value["parentStatus_new_count"] }}</td>
                              <td class="text-center">{{ $value["parentStatus_followUp_count"] }}</td>
                              <td class="text-center">{{ $value["parentStatus_success_count"] }}</td>
                              <td class="text-center">{{ $value["parentStatus_dumb_count"] }}</td>
                          </tr>
                        @endforeach
                          <tr>
                            <td class="text-primary fw-bold">---</td>
                            <td class="text-primary">
                              <span class="badge badge-center rounded-pill bg-label-primary">
                                <i class="fa-solid fa-calculator"></i>
                              </span>
                              <strong>
                                Total
                              </strong>
                            </td>
                            <td class="text-center text-primary fw-bold">{{ $SUM_total_call_count }}</td>
                            <td class="text-center text-primary fw-bold">{{ $SUM_parentStatus_new_count }}</td>
                            <td class="text-center text-primary fw-bold">{{ $SUM_parentStatus_followUp_count }}</td>
                            <td class="text-center text-primary fw-bold">{{ $SUM_parentStatus_success_count }}</td>
                            <td class="text-center text-primary fw-bold">{{ $SUM_parentStatus_dumb_count }}</td>
                          </tr>
                    @else
                    <tr>
                        <td colspan="7" class="text-center text-danger">No Telecallers Found</td>
                    </tr>
                    @endif
                  </tbody>
              </table>
          </div>

        </div>

      @endforeach

    @endif


    
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

<script>
$(document).ready(function(){

  // handling clear buttons of date fields
  let clearedByUser = false;
  $('#assigned_from_date, #assigned_to_date').on('input', function() {
      let fromEmpty = $('#assigned_from_date').val() === "";
      let toEmpty = $('#assigned_to_date').val() === "";

      // only true if both are cleared
      clearedByUser = (fromEmpty && toEmpty);
      if (clearedByUser)
      {
        $('.resetBtn').not('.d-none').addClass('d-none');
      }
  });

  // filter
  $(document).on('click', '.filterBtn', function()
  {
    let selected_assigned_from_date = $('#assigned_from_date').val();
    let selected_assigned_to_date = $('#assigned_to_date').val();

    if(selected_assigned_from_date == "" && selected_assigned_to_date == "")
    {
      $('.resetBtn').not('.d-none').addClass('d-none');
      toastAlert('error', 'Please Select Something To Apply Filter');

      // if both are cleared
      // if (clearedByUser)
      // {
      //   clearedByUser = false;
      //   let url = new URL(window.location.href);
      //   url.searchParams.delete('assigned-from-date');
      //   url.searchParams.delete('assigned-to-date');
      //   window.location.href = url.toString();
      // }
    }
    else
    {
      // alert('filter applied successfully');
      let url = new URL(window.location.href);

      if(selected_assigned_from_date != "" && selected_assigned_to_date != "")
      {
        url.searchParams.set('assigned-from-date', selected_assigned_from_date);
        url.searchParams.set('assigned-to-date', selected_assigned_to_date);
      }
      else if(selected_assigned_from_date != "")
      {
        url.searchParams.set('assigned-from-date', selected_assigned_from_date);
        url.searchParams.delete('assigned-to-date');
      }
      else if(selected_assigned_to_date != "")
      {
        url.searchParams.set('assigned-to-date', selected_assigned_to_date);
        url.searchParams.delete('assigned-from-date');
      }
      

      // Redirect once
      window.location.href = url.toString();
    }


  });


  // show reset button iff any filter is applied
  const currentUrl = new URL(window.location.href);
  if( (currentUrl.searchParams.has('assigned-from-date')) || (currentUrl.searchParams.has('assigned-to-date')) )
  {
    $('.resetBtn').removeClass('d-none');
    // $('.resetBtn').addClass('d-block');

    // toastAlert('success', 'Filter Applied Successfully !!!');
  }
  

  //reset
  $(document).on('click', '.resetBtn', function()
  {
    let url = new URL(window.location.href);
    // safe even if it's not there
    url.searchParams.delete('assigned-from-date');
    url.searchParams.delete('assigned-to-date');

    window.location.href = url.toString();
  });




  // download as CSV
  $(document).on('click', '.exportAsCSV', function() {
      let card = $(this).closest('.card');
      let branchName = card.find('.branchName').text().trim();
      let table = card.find('table');
      let csv = [];

      // Table headers
      let headers = [];
      table.find('thead th').each(function() {
          let text = $(this).text().replace(/\s+/g, ' ').trim();
          headers.push(text);
      });
      csv.push(headers.join(','));

      // Table rows
      table.find('tbody tr').each(function() {
          let rowData = [];
          $(this).find('td').each(function() {
            let html = $(this).html() || '';
            let text = html.replace(/<br\s*\/?>/gi, '\n');

            text = $('<div>').html(text).text();

            text = text
                .replace(/[ \t]+/g, ' ')
                .replace(/\n\s*/g, '\n')
                .replace(/^\n+|\n+$/g, '')
                .trim()
                .replace(/"/g, '""');

            // ✅ Only wrap in quotes if not empty
            if (text.length > 0) {
                rowData.push(`"${text}"`);
            } else {
                rowData.push(""); // keep cell empty without extra quotes
            }
        });

          csv.push(rowData.join(','));
      });

      // Add BOM for Excel and download
      let csvContent = "\uFEFF" + csv.join('\n');
      let blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
      let link = document.createElement("a");
      link.href = URL.createObjectURL(blob);

      let now = new Date();
      let formattedDateTime = now.getFullYear() + '-' +
      String(now.getMonth() + 1).padStart(2, '0') + '-' +
      String(now.getDate()).padStart(2, '0') + '_' +
      String(now.getHours()).padStart(2, '0') + '-' +
      String(now.getMinutes()).padStart(2, '0') + '-' +
      String(now.getSeconds()).padStart(2, '0');

      link.download = branchName + "_report_" + formattedDateTime + ".csv";
      link.click();

      toastAlert('success', 'File Exported Successfully !!!');
  });






  

});
</script>

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