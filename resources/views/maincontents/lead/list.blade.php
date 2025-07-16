<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <h4><?= $page_header ?></h4>
            <h6 class="breadcrumb-wrapper">
                <span class="text-muted fw-light"><a href="<?= url('dashboard') ?>">Dashboard</a> /</span> <?= $page_header ?>
            </h6>
            <div class="nav-align-top mb-4">
                <?php if(session('success_message')){?>
                <div class="alert alert-success alert-dismissible autohide" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-desktop align-top me-2"></i>Success!</h6>
                    <span><?= session('success_message') ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
                <?php }?>
                <?php if(session('error_message')){?>
                <div class="alert alert-danger alert-dismissible autohide" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-store align-top me-2"></i>Error!</h6>
                    <span><?= session('error_message') ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
                <?php }?>
                <div class="card mb-4">
                    {{-- <div class="card-header">
                        <a href="<?= url($controllerRoute . '/add/') ?>"
                            class="btn btn-outline-success btn-sm float-end">Add <?= $module['title'] ?></a>
                    </div> --}}
                    <div class="card-body">
                        {{-- <div id="table-overlay-loader" class="text-loader">
                  Fetching data. Please wait <span id="dot-animation">.</span>
               </div> --}}
                        <h6 class="card-title">Filter</h6>
                        <form class="mb-3">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <select id="filter_id" class="select2 form-select" data-allow-clear="true" required>

                                        <option value="" disable selected>Select Status</option>
                                        <option value="PHONE NOT CONNECTED">PHONE NOT CONNECTED</option>
                                        <option value="DID NOT PICKUP">DID NOT PICKUP</option>
                                        <option value="CALL LATER">CALL LATER</option>
                                        <option value="FOLLOW UP">FOLLOW UP</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <select id="" class="select2 form-select" data-allow-clear="true">
                                        <option value="" disable selected>ALL</option>

                                    </select>
                                </div>
                                <div class="col-4 col-md-2 mb-2">
                                    <button type="button" class="btn btn-outline-dark">
                                        <i class="fa-solid fa-filter"></i>&nbsp;<span>Filter Leads</span>
                                    </button>
                                </div>
                                <div class="col-8 col-md-2 mb-2">
                                    <button type="button" class="btn btn-label-secondary">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>&nbsp;<span>Reset</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <h6 class="card-title">Transfer Lead To</h6>
                        <form class="mb-5">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <select id="" class="select2 form-select" data-allow-clear="true" required>
                                        <option value="" disable selected>Select User</option>

                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button type="button" class="btn btn-outline-dark rounded-pill">
                                        <i class="fas fa-exchange-alt"></i>&nbsp;<span>Bulk Lead Transfer</span>
                                    </button>
                                </div>
                            </div>
                        </form>


                        <div class="card p-3">
                            <h5 class="card-header fw-bold text-success p-2">Lead List</h5>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="" id=""></th>
                                            <th>#</th>
                                            <th>Lead No</th>
                                            <th>Company</th>
                                            <th>Last Activity</th>
                                            <th>Scheduled Date | Time </th>
                                            <th>Assigned User</th>
                                            <th style="text-align: center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <tr>
                                            <td><input type="checkbox" name="" id=""></td>
                                            <td>1</td>
                                            <td>0000768</td>

                                            <td>
                                                <span class="badge badge-center rounded-pill bg-label-info mb-1">
                                                    <i class="fa-solid fa-building"></i>
                                                </span> <span class="fw-bold text-primary">Tsunami Technology</span>
                                                <br>
                                                <span class="badge badge-center rounded-pill bg-label-info mb-1">
                                                    <i class="fa-solid fa-phone"></i>
                                                </span> <span class="fw-bold text-primary">9887458965</span>
                                                <br>
                                                <span class="badge badge-center rounded-pill bg-label-info mb-1">
                                                    <i class="fa-solid fa-envelope"></i>
                                                </span> <span class="fw-bold text-primary">tsunami@gmail.com</span>
                                            </td>
                                            <td>
                                                <span>April 28, 2025</span>
                                                <br>
                                                <span class="badge bg-label-info me-1 mt-1">Follow Up</span>
                                                <br>
                                                <span class="badge rounded-pill bg-label-success text-dark mt-1">1 Update</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-center rounded-pill bg-label-warning">
                                                    <i class="fa-regular fa-clock"></i>
                                                </span> <span>Mar 03, 2025 03:23 PM</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-center rounded-pill bg-label-danger">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                </span> <span>Ganesh Gaitonde</span>
                                            </td>
                                            <td style="text-align: center">
                                                <button class="btn btn-sm btn-outline-dark mb-1" data-bs-toggle="modal"
                                                    data-bs-target="#callModal" title="Call">
                                                    <i class="fa-solid fa-headset"></i>&nbsp;<span>Call</span>
                                                </button>
                                                <br>
                                                <a href="" class="btn btn-sm btn-primary mb-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="" class="btn btn-sm btn-info mb-1" title="View">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                                <br>
                                                <a href="" class="btn btn-sm btn-success mb-1" title="Deactivate">
                                                    <i class="fa-solid fa-check"></i>
                                                </a>
                                                <a href="" class="btn btn-sm btn-danger mb-1"
                                                    onclick="return confirm('Are you sure?')" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                                <a href="" class="btn btn-sm btn-outline-dark mb-1"
                                                    title="Transfer Lead To Another User">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- call modal -->
                        <div class="modal fade" id="callModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                <div class="modal-content">
                                    <div class="modal-body p-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        {{-- modal body  --}}
                                        <h5 class="text-primary mb-4">Lead Activity : 0000768</h5>
                                        <div class="row">
                                            <!-- Left info side -->
                                            <div class="col-md-12">
                                                <!-- Lead Info and Edit -->
                                                <div class="row mb-2">
                                                    <div class="col-md-6 ">
                                                        <p class="mb-1 small">Lead ID: <strong>0000768</strong></p>
                                                        <p class="mb-1 small">Source: <strong>Plastic</strong></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1 small">Added on: <strong>Mar 03, 2025 03:23
                                                                PM</strong></p>
                                                        <p class="mb-1 small">Added by: <strong>Panna Admin</strong></p>
                                                    </div>
                                                </div>

                                                <!-- Lead Card -->
                                                <div id="leadDisplay"
                                                    class="border border-primary rounded p-3 bg-label-light text-primary mb-4">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <p class="mb-1 fw-bold"><span
                                                                    class="badge badge-center rounded-pill bg-label-secondary text-dark"><i
                                                                        class="fa-solid fa-user"></i></span> Paras</p>
                                                            <p class="mb-1"><span
                                                                    class="badge badge-center rounded-pill bg-label-secondary text-dark"><i
                                                                        class="fa-solid fa-phone"></i></span> 9831887018
                                                            </p>
                                                            <p class="mb-1 text-uppercase"><span
                                                                    class="badge badge-center rounded-pill bg-label-secondary text-dark"><i
                                                                        class="fa-solid fa-building"></i></span> PRASEEDA
                                                                EXIM LLP</p>
                                                            <p class="mb-1">
                                                                <a href="mailto:praseedae6@gmail.com"
                                                                    class="text-decoration-none text-primary"><span
                                                                        class="badge badge-center rounded-pill bg-label-secondary text-dark"><i
                                                                            class="fa-solid fa-envelope"></i></span>
                                                                    praseedae6@gmail.com</a>
                                                            </p>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-dark" type="button"
                                                            onclick="toggleEdit(true)">
                                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Edit Form -->
                                                <div id="leadEdit"
                                                    class="border-primary rounded p-3 bg-label-dark text-white mb-4 d-none">
                                                    <form>
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text border-primary"><i
                                                                    class="fa-solid fa-user"></i></span>
                                                            <input type="text"
                                                                class="form-control border-dark text-primary"
                                                                value="Paras">
                                                        </div>
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text border-dark"><i
                                                                    class="fa-solid fa-phone"></i></span>
                                                            <input type="text"
                                                                class="form-control border-dark text-primary"
                                                                value="9831887018">
                                                        </div>
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text border-dark"><i
                                                                    class="fa-solid fa-building"></i></span>
                                                            <input type="text"
                                                                class="form-control border-dark text-primary"
                                                                value="PRASEEDA EXIM LLP">
                                                        </div>
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text border-dark"><i
                                                                    class="fa-solid fa-envelope"></i></span>
                                                            <input type="email"
                                                                class="form-control border-dark text-primary"
                                                                value="praseedae6@gmail.com">
                                                        </div>

                                                        <div class="d-flex justify-content-end gap-2 mt-4">
                                                            <button class="btn btn-outline-dark btn-sm"
                                                                type="submit">Save</button>
                                                            <button class="btn btn-outline-danger btn-sm" type="button"
                                                                onclick="toggleEdit(false)">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div class="bg-label-success rounded p-3 mb-4">
                                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                                        <div class="d-flex align-items-center text-secondary small">
                                                            <span
                                                                class="badge badge-center rounded-pill bg-white text-dark">
                                                                <i class="fa-solid fa-headset"></i></span>&nbsp;<span
                                                                class="fw-bold text-primary">Jan 10, 2025
                                                                03:23 PM</span>
                                                        </div>
                                                        <span
                                                            class="badge bg-dark text-white me-1 border border-primary">Follow
                                                            Up</span>
                                                        <div class="ms-auto fw-semibold text-primary">Preeti</div>
                                                        {{-- <div class="rounded-circle bg-dark text-white d-flex justify-content-center align-items-center"
                                                            style="width: 36px; height: 36px; font-weight: bold; font-size: 1.1rem;">
                                                            P</div> --}}
                                                    </div>
                                                    <div
                                                        class="border-top border-bottom border-secondary-subtle py-2 small">
                                                        <span
                                                            class="badge badge-center rounded-pill bg-white text-dark">
                                                            <i
                                                                class="fa-solid fa-comment-dots text-primary"></i></span>&nbsp;asked
                                                        for
                                                        connect on first week of March
                                                    </div>
                                                    <div class="text-secondary d-flex align-items-center small">
                                                        <span
                                                            class="badge badge-center rounded-pill bg-white text-dark">
                                                            <i class="fa-solid fa-clock-rotate-left"></i></span>&nbsp;<span
                                                            class="mb-1 small">Next Schedule: <strong>Mar 03, 2025
                                                                03:23 PM</strong> </span>
                                                    </div>
                                                </div>

                                                <!-- Update Status Form -->
                                                <div
                                                    class="border border-dark rounded p-3 bg-label-white text-primary">
                                                    <form>
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                                            <small class="text-danger fst-italic">* (Star) Marks Fields Are
                                                                Mandatory</small>
                                                        </div>

                                                        <div class="mb-6">
                                                            <label for="leadStatus" class="form-label fw-bold">Update Lead
                                                                Status <span class="text-danger">*</span></label>
                                                            <select id="leadStatus"
                                                                class="select2 form-select border-primary text-primary"
                                                                required>
                                                                <option value="" selected disabled>Select Status
                                                                </option>
                                                                <option value="NOT QUALIFIED [DUMP]">NOT QUALIFIED [DUMP]
                                                                </option>
                                                                <option value="WRONG NUMBER [DUMP]">WRONG NUMBER [DUMP]
                                                                </option>
                                                                <option value="NOT INTERESTED [DUMP]">NOT INTERESTED [DUMP]
                                                                </option>
                                                                <option value="NO ENQUIRY [DUMP]">NO ENQUIRY [DUMP]
                                                                </option>
                                                                <option value="FOLLOW UP [LEADS]">FOLLOW UP [LEADS]
                                                                </option>
                                                                <option value="CALL LATER [LEADS]">CALL LATER [LEADS]
                                                                </option>
                                                                <option value="DID NOT PICKUP [LEADS]">DID NOT PICKUP
                                                                    [LEADS]</option>
                                                                <option value="PHONE NOT CONNECTED [LEADS]">PHONE NOT
                                                                    CONNECTED [LEADS]</option>
                                                                <option value="INTERESTED [QUALIFIED]">INTERESTED
                                                                    [QUALIFIED]</option>
                                                                <option value="PROSPECT LEADS [QUALIFIED]">PROSPECT LEADS
                                                                    [QUALIFIED]</option>
                                                            </select>
                                                        </div>

                                                        <div class="row g-3 mb-3">
                                                            <div class="col-sm-6">
                                                                <label for="nextFollowUpDate" class="form-label">Next
                                                                    Follow Up Date</label>
                                                                <input type="date" id="nextFollowUpDate"
                                                                    class="form-control border-primary text-primary"
                                                                    placeholder="mm/dd/yyyy" />
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label for="nextFollowUpTime" class="form-label">Next
                                                                    Follow Up Time</label>
                                                                <input type="time" id="nextFollowUpTime"
                                                                    class="form-control border-primary text-primary"
                                                                    placeholder="--:-- --" />
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="specialComment" class="form-label">Special
                                                                Comment</label>
                                                            <textarea id="specialComment" class="form-control border-primary text-primary" rows="3"
                                                                placeholder="Special Comment"></textarea>
                                                        </div>

                                                        <button type="submit" class="btn btn-outline-dark btn-sm">Save
                                                            Changes</button>
                                                    </form>

                                                </div>
                                            </div>


                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- call modal -->

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleEdit(showEdit) {
            document.getElementById('leadDisplay').classList.toggle('d-none', showEdit);
            document.getElementById('leadEdit').classList.toggle('d-none', !showEdit);
        }
    </script>
@endsection
@section('scripts')
    <script src="<?= config('constants.admin_assets_url') ?>assets/js/table.js"></script>
@endsection
