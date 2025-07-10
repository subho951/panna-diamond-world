<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
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
                                <div class="col-md-2 mb-2">
                                    <button type="button" class="btn btn-label-primary">
                                        <i class="fa-solid fa-filter"></i>&nbsp;<span>Filter Leads</span>
                                    </button>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <button type="button" class="btn btn-label-danger">
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
                              <button type="button" class="btn btn-label-primary rounded-pill">
                                <i class="fas fa-exchange-alt"></i>&nbsp;<span>Bulk Lead Transfer</span>
                              </button>
                          </div>
                          </div>
                        </form>
                        

                        <div class="card">
                            <h5 class="card-header fw-bold text-success">Lead List</h5>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="" id=""></th>
                                            <th>#</th>
                                            <th>Lead No</th>
                                            <th>Company</th>
                                            <th>Last Activity</th>
                                            <th>Scheduled Date | Time </th>
                                            <th>Assigned User</th>
                                            <th>Actions</th>
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
                                                <span class="badge bg-label-primary me-1 mt-1">Follow Up</span>
                                                <br>
                                                <span class="badge rounded-pill bg-label-info mt-1">1 Update</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-center rounded-pill bg-label-primary">
                                                    <i class="fa-regular fa-clock"></i>
                                                </span> <span>Mar 03, 2025 03:23 PM</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-center rounded-pill bg-label-info">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                </span> <span>Ganesh Gaitonde</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm bg-label-primary mb-1" data-bs-toggle="modal"
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
                                                <a href="" class="btn btn-sm bg-label-primary mb-1"
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
                                    <div class="modal-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        {{-- modal body  --}}
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
@endsection
@section('scripts')
    <script src="<?= config('constants.admin_assets_url') ?>assets/js/table.js"></script>
@endsection
