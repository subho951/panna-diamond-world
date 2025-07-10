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
                                        <div class="modal-body" id="leadActivityBody"><div class="calllead_allinfo">
                                            <div class="row">
                                               <div class="col-md-6">
                                                  <div class="calllead_top">
                                                     <div class="row">
                                                        <div class="col-md-6 pr-0">
                                                           <p>Lead ID: 0000768</p>
                                                           <p>Source: Plastic</p>
                                                        </div>
                                                        <div class="col-md-6 px-0">
                                                           <p>Added on: Mar 03, 2025 03:23 PM</p>
                                                           <p>Added by: Ecoex Admin</p>
                                                        </div>
                                                     </div>
                                                  </div>
                                                  <div class="calllead_userwhite">
                                                     <div class="calledit_user">
                                                        <button type="button" id="lead-info-edit" onclick="enableDataEdit(768);" class="btn btn-info btn-sm"><i class="fas fa-pen"></i> Edit</button>
                                                        <button type="button" id="lead-info-save" onclick="updateDataEditLead(768);" style="background: #6ABF12; display: none;" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
                                                        <button type="button" id="lead-info-close" onclick="disableDataEdit(768);" style="background: #ff0000; display: none;" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Cancel</button>
                                                     </div>
                                                     <ul>
                                                        <li class="lead-main-info-label">
                                                           <i class="fas fa-user"></i> <span class="fontbig">Paras</span>
                                                        </li>
                                                        <li class="lead-main-info-input" style="display: none;">
                                                           <i class="fas fa-user"></i> 
                                                           <input type="text" class="form-control" name="contact_person_name" id="contact_person_name768" value="Paras">
                                                        </li>
                                         
                                                        <li class="lead-main-info-label">
                                                           <i class="fas fa-mobile-alt"></i> <span class="fontbig">9831887018</span>
                                                           <!-- <a href="" class="whatsaddria"><i class="fab fa-whatsapp"></i></a> -->
                                                        </li>
                                                        <li class="lead-main-info-input" style="display: none;">
                                                           <i class="fas fa-mobile-alt"></i>
                                                           <input type="text" class="form-control" name="contact_number" id="contact_number768" value="9831887018">
                                                        </li>
                                         
                                                        <li class="lead-main-info-label"><i class="fas fa-building"></i> <span class="fontmed">PRASEEDA EXIM LLP</span></li>
                                                        <li class="lead-main-info-input" style="display: none;">
                                                           <i class="fas fa-building"></i>
                                                           <input type="text" class="form-control" name="stakeholder_name" id="stakeholder_name768" value="PRASEEDA EXIM LLP">
                                                        </li>
                                         
                                                        <li class="lead-main-info-label"><i class="fas fa-envelope"></i> <span class="fontmed"><a href="mailto:praseedae6@gmail.com">praseedae6@gmail.com</a></span></li>
                                                        <li class="lead-main-info-input" style="display: none;">
                                                           <i class="fas fa-envelope"></i>
                                                           <input type="text" class="form-control" name="email" id="email768" value="praseedae6@gmail.com">
                                                        </li>
                                         
                                                        <li class="lead-main-info-label"><i class="fas fa-list-alt"></i> <span class="fontmed"></span></li>
                                                        <li class="lead-main-info-input" style="display: none;">
                                                           <i class="fas fa-envelope"></i>
                                                           <select class="form-control" name="stakeholder_type" id="stakeholder_type768">
                                                              <option value="" selected="">Select Client Type</option>
                                                                                      <option value="Producer">Producer</option>
                                                                                      <option value="Importer">Importer</option>
                                                                                      <option value="Brand - Plastic">Brand - Plastic</option>
                                                                                      <option value="Brand - E-Waste">Brand - E-Waste</option>
                                                                                      <option value="Brand - Battery">Brand - Battery</option>
                                                                                      <option value="Brand - Tyre">Brand - Tyre</option>
                                                                                      <option value="PWP - Plastic">PWP - Plastic</option>
                                                                                      <option value="PWP - Rubber &amp; Tyre">PWP - Rubber &amp; Tyre</option>
                                                                                      <option value="PWP - Battery">PWP - Battery</option>
                                                                                      <option value="PWP&nbsp;-&nbsp;E-Waste">PWP&nbsp;-&nbsp;E-Waste</option>
                                                                                </select>
                                                        </li>
                                                     </ul>
                                                  </div>
                                                  <div class="calllead_contactperson" style="border: 1px solid #0080004f;padding: 15px;border-radius: 10px;margin-top: 10px;">
                                                     <div class="calllead_contactper_btn">
                                                        <a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">Update Status <i class="fas fa-caret-down"></i></a>
                                                     </div>
                                                     <form method="post" action="">
                                                        <span class="text-danger">* (Star) Marks Fields Are Mandatory</span>
                                                        <input type="hidden" id="parent_id" name="parent_id" value="">
                                                        <input type="hidden" id="agent" name="agent" value="64">
                                                        <input type="hidden" id="lead_id" name="lead_id" value="11602">
                                                        <input type="hidden" id="lead_sl" name="lead_sl" value="768">
                                                        <input type="hidden" id="updatemode" name="updatemode" value="updateleadstatus">
                                                        <div class="collapse show callstatus_boxing" id="multiCollapseExample1">
                                                           <div class="calllead_statusbox">
                                                              <div class="row">
                                                                 <div class="col-md-12">
                                                                    <div class="form-group">
                                                                       <label for="name">Update Lead Status <span class="text-danger">*</span></label>
                                                                       <select class="form-control" name="lead_status" id="lead_child" onchange="putFollowUpdate(768,this.value)" required="">
                                                                          <option value="" selected="">Select Status</option>
                                                                                                                                               <option value="37">NOT QUALIFIED [DUMP]</option>
                                                                                                                                               <option value="11">WRONG NUMBER [DUMP]</option>
                                                                                                                                               <option value="12">NOT INTERESTED [DUMP]</option>
                                                                                                                                               <option value="13">NO ENQUIRY [DUMP]</option>
                                                                                                                                               <option value="36">FOLLOW UP [LEADS]</option>
                                                                                                                                               <option value="14">CALL LATER [LEADS]</option>
                                                                                                                                               <option value="10">DID NOT PICKUP [LEADS]</option>
                                                                                                                                               <option value="9">PHONE NOT CONNECTED [LEADS]</option>
                                                                                                                                               <option value="15">INTERESTED [QUALIFIED]</option>
                                                                                                                                               <option value="35">PROSPECT LEADS [QUALIFIED]</option>
                                                                                                        </select>
                                                                    </div>
                                                                 </div>
                                                                 <div class="col-md-6">
                                                                    <div class="form-group" id="followup_date_row_768">
                                                                       <label for="name">Next Follow Up Date</label>
                                                                       <input type="date" class="form-control" name="next_followup_date" id="next_followup_date_768">
                                                                    </div>
                                                                 </div>
                                                                 <div class="col-md-6">
                                                                    <div class="form-group" id="followup_time_row_768">
                                                                       <label for="name">Next Follow Up Time</label>
                                                                       <input type="time" class="form-control" name="next_followup_time" id="next_followup_time_768">
                                                                    </div>
                                                                 </div>
                                                                 <div class="col-md-12">
                                                                    <div class="form-group" id="followup_comment_row_768">
                                                                       <label for="password"><span id="special_comment_label">Special Comment</span></label>
                                                                       <textarea class="form-control" name="orher_remarks" id="special_comment_768" placeholder="Special Comment" rows="3"></textarea>
                                                                    </div>
                                                                 </div>
                                                              </div>
                                                           </div>
                                                           <div class="callstatus_btn">
                                                              <!-- <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#multiCollapseExample1" aria-expanded="false" aria-controls="multiCollapseExample1">Close</button> -->
                                                              <button class="btn btn-primary" type="submit">Save Changes</button>
                                                           </div>
                                                        </div>
                                                     </form>
                                                  </div>
                                               </div>
                                               <div class="col-md-6">
                                                  <div class="calllead_alllistinfo" style="height: 702px !important;">
                                                                    <div class="calllead_alllistinfo_item">
                                                           <div class="called_alllist_top">
                                                              <div class="calllall_left">
                                                                 <div class="roundcall"><i class="fas fa-phone-alt"></i></div>
                                                              </div>
                                                              <div class="callall_lefttime">
                                                                 <p>Jan 10, 2025 03:23 PM</p>
                                                                 <div class="callstus">FOLLOW UP</div>
                                                              </div>
                                                              <div class="callall_leftvistor">
                                                                 <h5>Preeti</h5>
                                                                 <!-- <img src="https://stagingmarket.ecoex.market/public/assets/newadmin/images/demo_crm.jpg" alt="img"> -->
                                                                                         <img src="https://stagingmarket.ecoex.market/public/no-image.png" alt="Preeti">
                                                              </div>
                                                           </div>
                                                           <div class="called_alllist_bottom">
                                                              <div class="calllall_left">
                                                                 <div class="calllall_left_commicon"><i class="fas fa-comment-alt"></i></div>
                                                              </div>
                                                              <div class="calllall_left_comment">
                                                                 <p>asked for connect on first week of March</p>
                                                              </div>
                                                           </div>
                                                           <div class="callall_timer">
                                                              <p><i class="fas fa-history"></i> Next Schedule: Mar 03, 2025 03:23 PM</p>
                                                           </div>
                                                        </div>
                                                              </div>
                                               </div>
                                            </div>
                                         </div></div>
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
