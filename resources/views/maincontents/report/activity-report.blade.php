<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <h4><?=$page_header?></h4>
            <h6 class="breadcrumb-wrapper">
                <span class="text-muted fw-light"><a href="<?=url('dashboard')?>">Dashboard</a> /</span> <?=$page_header?>
            </h6>
            <div class="nav-align-top mb-4">
                <?php if (session('success_message')) {?>
                <div class="alert alert-success alert-dismissible autohide" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-desktop align-top me-2"></i>Success!</h6>
                    <span><?=session('success_message')?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
                <?php }?>
                <?php if (session('error_message')) {?>
                <div class="alert alert-danger alert-dismissible autohide" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-store align-top me-2"></i>Error!</h6>
                    <span><?=session('error_message')?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
                <?php }?>


                {{-- filter section start --}}
                <div class="card mb-3 p-3">
                    <form>
                        @csrf
                        <div class="row">
                            <div class="col-md-2 mt-3 d-flex align-items-center">
                                <div class="form-check form-switch ps-0">
                                    <label class="form-check-label" for="uniqueCall">Unique Calls</label>
                                    <input class="form-check-input float-none ms-0" type="checkbox" name="uniqueCall" role="switch" id="uniqueCall"
                                    @if(!empty($unique_check)) checked @endif>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="call_from_date" class="form-label">Call From </label>
                                        <input class="form-control" type="date" id="call_from_date" name="call_from_date"
                                            @if(!empty($callFromDate)) value="{{ $callFromDate }}" @else
                                            value="<?=date('Y-m-d')?>" @endif max="<?=date('Y-m-d')?>" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="call_to_date" class="form-label">Call To </label>
                                        <input class="form-control" type="date" id="call_to_date" name="call_to_date"
                                            @if(!empty($callToDate)) value="{{ $callToDate }}" @else
                                            value="<?=date('Y-m-d')?>" @endif max="<?=date('Y-m-d')?>" />
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
                                        <span
                                            class="branchName card-header fw-bold h6 ps-0">{{ $eachBranchWiseTelecallerActivity["branch_name"] }}</span>
                                    </div>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Telecaller | Last Call</th>
                                                <th class="text-center">Total Calls</th>
                                                {{-- <th class="text-center">Pending</th> --}}
                                                <th class="text-center">Follow Up</th>
                                                <th class="text-center">Success</th>
                                                <th class="text-center">Dump</th>
                                                <th class="text-center">All Time Pending</th>
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
                                                                <strong class="telecallerName">{{ $value["telecaller_name"] }} </strong>
                                                                <br>
                                                            @endif
                                                            @if(!empty($value["last_call_of_telecaller"]))
                                                                <span class="badge badge-center rounded-pill bg-label-warning mt-1">
                                                                    <i class="fa-solid fa-headset"></i>
                                                                </span>
                                                                <strong class="text-muted">{{ $value["last_call_of_telecaller"]}}</strong>
                                                            @endif
                                                        </td>
                                                        <td class="text-center"><span
                                                                class="badge bg-label-primary cursor-pointer activityReportModalBtn"
                                                                data-bs-toggle="modal" data-bs-target=".activityReportModal"
                                                                data-leadactivityidarr='@json($value['total_call_count_idArr'])'>{{ $value["total_call_count"] }}</span>
                                                        </td>
                                                        {{-- <td class="text-center">{{ $value["parentStatus_new_count"] }}</td> --}}
                                                        <td class="text-center"><span
                                                                class="badge bg-label-info cursor-pointer activityReportModalBtn"
                                                                data-bs-toggle="modal" data-bs-target=".activityReportModal"
                                                                data-leadactivityidarr='@json($value['parentStatus_followUp_count_idArr'])'>{{ $value["parentStatus_followUp_count"] }}</span>
                                                        </td>
                                                        <td class="text-center"><span
                                                                class="badge bg-label-success cursor-pointer activityReportModalBtn"
                                                                data-bs-toggle="modal" data-bs-target=".activityReportModal"
                                                                data-leadactivityidarr='@json($value['parentStatus_success_count_idArr'])'>{{ $value["parentStatus_success_count"] }}</span>
                                                        </td>
                                                        <td class="text-center"><span
                                                                class="badge bg-label-danger cursor-pointer activityReportModalBtn"
                                                                data-bs-toggle="modal" data-bs-target=".activityReportModal"
                                                                data-leadactivityidarr='@json($value['parentStatus_dumb_count_idArr'])'>{{ $value["parentStatus_dumb_count"] }}</span>
                                                        </td>
                                                        <td class="text-center"><span class="badge rounded-pill bg-label-warning">
                                                                {{ $value['tellecallerWisePendingCount'] }} </span></td>
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
                                    <span
                                        class="branchName card-header fw-bold h6 p-0">{{ $eachBranchWiseTelecallerActivity["branch_name"] }}</span>
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
                                            {{-- <th class="text-center">Pending</th> --}}
                                            <th class="text-center">Follow Up</th>
                                            <th class="text-center">Success</th>
                                            <th class="text-center">Dump</th>
                                            <th class="text-center">All Time Pending</th>

                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @if(!empty($eachBranchWiseTelecallerActivity["telecallerActivity"]))

                                            @php
                                                $SUM_total_call_count = 0;
                                                $SUM_parentStatus_new_count = 0;
                                                $SUM_parentStatus_followUp_count = 0;
                                                $SUM_parentStatus_success_count = 0;
                                                $SUM_parentStatus_dumb_count = 0;

                                                $SUM_tellecallerWisePendingCount = 0;

                                                $MERGE_parentStatus_new_count_idArr = [];
                                                $MERGE_parentStatus_dumb_count_idArr = [];
                                                $MERGE_parentStatus_followUp_count_idArr = [];
                                                $MERGE_parentStatus_success_count_idArr = [];
                                                $MERGE_total_call_count_idArr = [];
                                            @endphp

                                            @foreach($eachBranchWiseTelecallerActivity["telecallerActivity"] as $key => $value)
                                                {{-- @dd($value); --}}
                                                @php
                                                    $SUM_total_call_count = $SUM_total_call_count + $value["total_call_count"];
                                                    $SUM_parentStatus_new_count = $SUM_parentStatus_new_count + $value["parentStatus_new_count"];
                                                    $SUM_parentStatus_followUp_count = $SUM_parentStatus_followUp_count + $value["parentStatus_followUp_count"];
                                                    $SUM_parentStatus_success_count = $SUM_parentStatus_success_count + $value["parentStatus_success_count"];
                                                    $SUM_parentStatus_dumb_count = $SUM_parentStatus_dumb_count + $value["parentStatus_dumb_count"];

                                                    $SUM_tellecallerWisePendingCount = $SUM_tellecallerWisePendingCount + $value['tellecallerWisePendingCount'];

                                                    $MERGE_total_call_count_idArr = array_merge($MERGE_total_call_count_idArr, $value["total_call_count_idArr"]);
                                                    $MERGE_parentStatus_new_count_idArr = array_merge($MERGE_parentStatus_new_count_idArr, $value["parentStatus_new_count_idArr"]);
                                                    $MERGE_parentStatus_followUp_count_idArr = array_merge($MERGE_parentStatus_followUp_count_idArr, $value["parentStatus_followUp_count_idArr"]);
                                                    $MERGE_parentStatus_success_count_idArr = array_merge($MERGE_parentStatus_success_count_idArr, $value["parentStatus_success_count_idArr"]);
                                                    $MERGE_parentStatus_dumb_count_idArr = array_merge($MERGE_parentStatus_dumb_count_idArr, $value["parentStatus_dumb_count_idArr"]);
                                                @endphp

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="telecaller_lastcall">
                                                        @if(!empty($value["telecaller_name"]))
                                                            <span class="badge badge-center rounded-pill bg-label-danger">
                                                                <i class="fa-solid fa-user-tie"></i>
                                                            </span>
                                                            <strong class="telecallerName">{{ $value["telecaller_name"] }} </strong><br>
                                                        @endif
                                                        @if(!empty($value["last_call_of_telecaller"]))
                                                            <span class="badge badge-center rounded-pill bg-label-warning mt-1">
                                                                <i class="fa-solid fa-headset"></i>
                                                            </span>
                                                            <strong class="text-muted">{{ $value["last_call_of_telecaller"]}}</strong>
                                                        @endif
                                                    </td>



                                                    <td class="text-center"><span
                                                            class="badge bg-label-primary cursor-pointer activityReportModalBtn"
                                                            data-bs-toggle="modal" data-bs-target=".activityReportModal"
                                                            data-leadactivityidarr='@json($value['total_call_count_idArr'])'>
                                                            {{ $value["total_call_count"] }} </span></td>

                                                    {{-- <td class="text-center">{{ $value["parentStatus_new_count"] }}</td> --}}

                                                    <td class="text-center"><span
                                                            class="badge bg-label-info cursor-pointer activityReportModalBtn"
                                                            data-bs-toggle="modal" data-bs-target=".activityReportModal"
                                                            data-leadactivityidarr='@json($value['parentStatus_followUp_count_idArr'])'>
                                                            {{ $value["parentStatus_followUp_count"] }} </span></td>

                                                    <td class="text-center"><span
                                                            class="badge bg-label-success cursor-pointer activityReportModalBtn"
                                                            data-bs-toggle="modal" data-bs-target=".activityReportModal"
                                                            data-leadactivityidarr='@json($value['parentStatus_success_count_idArr'])'>
                                                            {{ $value["parentStatus_success_count"] }} </span></td>

                                                    <td class="text-center"><span
                                                            class="badge bg-label-danger cursor-pointer activityReportModalBtn"
                                                            data-bs-toggle="modal" data-bs-target=".activityReportModal"
                                                            data-leadactivityidarr='@json($value['parentStatus_dumb_count_idArr'])'>
                                                            {{ $value["parentStatus_dumb_count"] }} </span></td>

                                                    <td class="text-center"><span class="badge rounded-pill bg-label-warning">
                                                            {{ $value['tellecallerWisePendingCount'] }} </span></td>
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
                                                <td class="text-center text-primary fw-bold"><span
                                                        class="badge bg-primary bg-glow cursor-pointer activityReportModalBtn"
                                                        data-bs-toggle="modal" data-bs-target=".activityReportModal" data-total="total"
                                                        data-leadactivityidarr='@json($MERGE_total_call_count_idArr)'>
                                                        {{ $SUM_total_call_count }} </span></td>

                                                {{-- <td class="text-center text-primary fw-bold">{{ $SUM_parentStatus_new_count }}</td>
                                                --}}

                                                <td class="text-center text-primary fw-bold"><span
                                                        class="badge bg-info bg-glow cursor-pointer activityReportModalBtn"
                                                        data-bs-toggle="modal" data-bs-target=".activityReportModal" data-total="total"
                                                        data-leadactivityidarr='@json($MERGE_parentStatus_followUp_count_idArr)'>
                                                        {{ $SUM_parentStatus_followUp_count }} </span></td>

                                                <td class="text-center text-primary fw-bold"><span
                                                        class="badge bg-success bg-glow cursor-pointer activityReportModalBtn"
                                                        data-bs-toggle="modal" data-bs-target=".activityReportModal" data-total="total"
                                                        data-leadactivityidarr='@json($MERGE_parentStatus_success_count_idArr)'>
                                                        {{ $SUM_parentStatus_success_count }} </span></td>

                                                <td class="text-center text-primary fw-bold"><span
                                                        class="badge bg-danger bg-glow cursor-pointer activityReportModalBtn"
                                                        data-bs-toggle="modal" data-bs-target=".activityReportModal" data-total="total"
                                                        data-leadactivityidarr='@json($MERGE_parentStatus_dumb_count_idArr)'>
                                                        {{ $SUM_parentStatus_dumb_count }} </span></td>

                                                <td class="text-center text-primary fw-bold"><span
                                                        class="badge rounded-pill bg-warning bg-glow">
                                                        {{ $SUM_tellecallerWisePendingCount }} </span></td>

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


            </div>
        </div>
    </div>

    {{-- <button type="button" class="btn btn-primary activityReportModalBtn" data-bs-toggle="modal"
        data-bs-target=".activityReportModal">
        Open
    </button> --}}


    <div id="loadingOverlay"
        class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex flex-column justify-content-center align-items-center"
        style="z-index: 9999;">

        <div class="spinner-border text-white mb-3" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>

        <h5 class="text-white fw-semibold">Please Wait ☕</h5>
        <h6 class="text-white mb-0">Processing Activity Data…</h6>

        {{-- <div class="card">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="spinner-border text-dark mb-3" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>

                <h5 class="text-dark fw-semibold">Please Wait ☕</h5>
                <h6 class="text-dark mb-0">Processing Activity Data…</h6>
            </div>
        </div> --}}

    </div>



@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            let baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
            const base_url = document.querySelector('meta[name="baseurl"]').getAttribute('content');

            // handling clear buttons of date fields
            let clearedByUser = false;
            $('#call_from_date, #call_to_date').on('input', function () {
                let fromEmpty = $('#call_from_date').val() === "";
                let toEmpty = $('#call_to_date').val() === "";

                // only true if both are cleared
                clearedByUser = (fromEmpty && toEmpty);
                if (clearedByUser) {
                    $('.resetBtn').not('.d-none').addClass('d-none');
                }
            });

            // filter
            $(document).on('click', '.filterBtn', function () {
                // alert('clicked');
                let selected_call_from_date = $('#call_from_date').val();
                let selected_call_to_date = $('#call_to_date').val();

                if (selected_call_from_date == "" && selected_call_to_date == "") {
                    $('.resetBtn').not('.d-none').addClass('d-none');
                    toastAlert('error', 'Please Select Something To Apply Filter');

                    // if both are cleared
                    // if (clearedByUser)
                    // {
                    //   clearedByUser = false;
                    //   let url = new URL(window.location.href);
                    //   url.searchParams.delete('call-from-date');
                    //   url.searchParams.delete('call-to-date');
                    //   window.location.href = url.toString();
                    // }
                }
                else {
                    // alert('filter applied successfully');
                    let url = new URL(window.location.href);

                    if (selected_call_from_date != "" && selected_call_to_date != "") {
                        url.searchParams.set('call-from-date', selected_call_from_date);
                        url.searchParams.set('call-to-date', selected_call_to_date);
                    }
                    else if (selected_call_from_date != "") {
                        url.searchParams.set('call-from-date', selected_call_from_date);
                        url.searchParams.delete('call-to-date');
                    }
                    else if (selected_call_to_date != "") {
                        url.searchParams.set('call-to-date', selected_call_to_date);
                        url.searchParams.delete('call-from-date');
                    }


                    // Redirect once
                    window.location.href = url.toString();
                }


            });


            // show reset button iff any filter is applied
            const currentUrl = new URL(window.location.href);
            if ((currentUrl.searchParams.has('call-from-date')) || (currentUrl.searchParams.has('call-to-date'))) {
                $('.resetBtn').removeClass('d-none');
                // $('.resetBtn').addClass('d-block');

                // toastAlert('success', 'Filter Applied Successfully !!!');
            }


            //reset
            $(document).on('click', '.resetBtn', function () {
                let url = new URL(window.location.href);
                // safe even if it's not there
                url.searchParams.delete('call-from-date');
                url.searchParams.delete('call-to-date');

                window.location.href = url.toString();
            });



            // unique calls
            $(document).on('change', '#uniqueCall', function ()
            {
                let url = new URL(window.location.href);

                if ($(this).is(':checked'))
                {
                    // alert('checked');
                    let uniqueCall = $(this).is(':checked');
                    url.searchParams.set('unique', uniqueCall);
                } 
                else
                {
                    // alert('unchecked');
                    url.searchParams.delete('unique');
                }

                window.location.href = url.toString();
            });

            



            // download as CSV
            $(document).on('click', '.exportAsCSV', function () {
                let card = $(this).closest('.card');
                let branchName = card.find('.branchName').text().trim();
                let table = card.find('table');
                let csv = [];

                // Table headers
                let headers = [];
                table.find('thead th').each(function () {
                    let text = $(this).text().replace(/\s+/g, ' ').trim();
                    headers.push(text);
                });
                csv.push(headers.join(','));

                // Table rows
                table.find('tbody tr').each(function () {
                    let rowData = [];
                    $(this).find('td').each(function () {
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

                link.download = branchName + "_activity_report_" + formattedDateTime + ".csv";
                link.click();

                toastAlert('success', 'File Exported Successfully !!!');
            });




            // activity report modal with custom loader
            let leadActivityIdArr;
            $(document).on('click', '.activityReportModalBtn', function () {
                leadActivityIdArr = $(this).data('leadactivityidarr');

                let branchName = $(this).closest('.card').find('.branchName').text().trim();

                let assignedTelecallerName = null;
                
                let total = false;
                if ($(this).data('total'))
                {
                    total = true;
                }
                else
                {
                    assignedTelecallerName = $(this).closest('tr').find('.telecallerName').text().trim();
                }

                // alert(branchName + ' | ' + assignedTelecallerName);
                // console.log(leadActivityIdArr);

                $.ajax({
                    url: base_url + '/activity-report-modal',
                    type: 'POST',
                    data: { leadActivityIdArr: leadActivityIdArr, total: total , branchName: branchName , assignedTelecallerName: assignedTelecallerName },
                    beforeSend: function () {
                        // Show Bootstrap overlay
                        $('#loadingOverlay').removeClass('d-none');
                    },

                    success: function (response) {

                        // Hide overlay
                        $('#loadingOverlay').addClass('d-none');

                        // Remove any existing modal with same class
                        $('.activityReportModal').remove();

                        // Remove any leftover Bootstrap backdrop
                        // $('.modal-backdrop').remove();

                        // Append the modal HTML to body
                        $('body').append(response.html);

                        // Initialize and show Bootstrap modal
                        var myModal = new bootstrap.Modal(document.querySelector('.activityReportModal'));
                        myModal.show();

                        // Optional: remove modal from DOM after hidden to prevent accumulation
                        document.querySelector('.activityReportModal').addEventListener('hidden.bs.modal', function () {
                            $(this).remove();
                        });

                    },
                    error: function (xhr) {
                        $('#loadingOverlay').addClass('d-none');
                        alert('Error Loading Activity data.');
                        console.log(xhr);
                    }

                });

            });


            




            // download as CSV (activity report modal)
            $(document).on('click', '.exportAsCSVInDetail', function () {
                let csvName = $(this).data('csvname');
                let card = $(this).closest('.detailActivityReportModal');
                let table = card.find('table');
                let csv = [];

                // Table headers
                let headers = [];
                table.find('thead th').each(function () {
                    let text = $(this).text().replace(/\s+/g, ' ').trim();
                    headers.push(text);
                });
                csv.push(headers.join(','));

                // Table rows
                table.find('tbody tr').each(function () {
                    let rowData = [];
                    $(this).find('td').each(function () {
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

                link.download = csvName + "_detailed-activity-report_" + formattedDateTime + ".csv";
                link.click();

                toastAlert('success', 'File Exported Successfully !!!');
            });


        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
    <script>
        function toastAlert(type, message, redirectStatus = false, redirectUrl = '') {
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
            if (redirectStatus) {
                setTimeout(function () { window.location = redirectUrl; }, 3000);
            }
        }
        // toastAlert('success', 'suceess message');
        // toastAlert('error', 'error message');
        // toastAlert('warning', 'warning message');
        // toastAlert('info', 'info message');
    </script>

@endsection