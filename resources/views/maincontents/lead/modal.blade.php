<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<h5 class="text-primary mb-4 leadActivity">Lead Activity({{ $leadActivityCount["lead_activity_count"]}}) : <span class="badge bg-label-primary">{{ $leadActivityCount["lead_no"] }}</span></h5>

<div class="row">
    <!-- Left info side -->
    <div class="col-md-6">
        <!-- Lead Info and Edit -->
        <div class="row mb-2 creaded_updated">
            {{-- <div class="col-md-6">
                <p class="mb-1 small">Added By: <strong>Panna Admin</strong></p>
                <p class="mb-1 small">Added On: <strong>Mar 03, 2025 03:23 PM</strong></p>
            </div>
            <div class="col-md-6">
                <p class="mb-1 small">Updated By: <strong>Panna Admin</strong></p>
                <p class="mb-1 small">Updated On: <strong>Mar 03, 2025 03:23 PM</strong></p>
            </div> --}}
            @if(!empty($addedUpdated["added_by_name"]))
                <p class="mb-1 small">Added By: <strong>{{ $addedUpdated["added_by_name"] }}</strong></p>
            @endif
            @if(!empty($addedUpdated["created_at"]))
                <p class="mb-1 small">Added On: <strong>{{ $addedUpdated["created_at"] }}</strong></p>
            @endif
        </div>
    
        <!-- Lead Card -->
        <div id="leadDisplay" class="border border-primary rounded p-3 bg-label-light text-primary mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                
                <div>
                    @foreach ($eachLeadArr as $leadData)
                    
                        @if(!empty($leadData['contact-person-name']) && $leadData['is_visible_in_lead_list'] == 'YES')
                            <p class="mb-1 fw-bold">
                                <span class="badge badge-center rounded-pill bg-label-secondary text-dark">
                                    <i class="fa-solid fa-user"></i>
                                </span> {{ $leadData['contact-person-name'] }}
                            </p>
                        @endif
                        @if(!empty($leadData['phone']) && $leadData['is_visible_in_lead_list'] == 'YES')
                            <p class="mb-1">
                                <span class="badge badge-center rounded-pill bg-label-secondary text-dark">
                                    <i class="fa-solid fa-phone"></i>
                                </span> {{$leadData['phone']}}
                            </p>
                        @endif
                        @if(!empty($leadData['email']) && $leadData['is_visible_in_lead_list'] == 'YES')
                            <p class="mb-1">
                                <a href="mailto:${leadData['email'].value}" class="text-decoration-none text-primary">
                                    <span class="badge badge-center rounded-pill bg-label-secondary text-dark">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    {{$leadData['email']}}
                                </a>
                            </p>
                        @endif
                        @if(!empty($leadData['whatsapp-number']) && $leadData['is_visible_in_lead_list'] == 'YES')
                            <p class="mb-1">
                                <span class="badge badge-center rounded-pill bg-label-secondary text-dark">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </span> {{$leadData['whatsapp-number']}}
                            </p>
                        @endif    

                    @endforeach
                </div>
            </div>
        </div>
    
        <!-- Edit Form -->
        <div id="leadEdit" class="border-primary rounded p-3 bg-label-dark text-white mb-4 d-none">
        <form>
            <div class="input-group mb-2">
            <span class="input-group-text border-primary"><i class="fa-solid fa-user"></i></span>
            <input type="text" class="form-control border-dark text-primary" value="Paras">
            </div>
            <div class="input-group mb-2">
            <span class="input-group-text border-dark"><i class="fa-solid fa-phone"></i></span>
            <input type="text" class="form-control border-dark text-primary" value="9831887018">
            </div>
            <div class="input-group mb-2">
            <span class="input-group-text border-dark"><i class="fa-solid fa-building"></i></span>
            <input type="text" class="form-control border-dark text-primary" value="PRASEEDA EXIM LLP">
            </div>
            <div class="input-group mb-2">
            <span class="input-group-text border-dark"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" class="form-control border-dark text-primary" value="praseedae6@gmail.com">
            </div>
    
            <div class="d-flex justify-content-end gap-2 mt-4">
            <button class="btn btn-outline-dark btn-sm" type="submit">Save</button>
            <button class="btn btn-outline-danger btn-sm" type="button" onclick="toggleEdit(false)">Cancel</button>
            </div>
        </form>
        </div>
    
        <!-- Update Status Form -->
        <div class="border border-dark rounded p-3 bg-label-white text-primary">
        <form id="updateLeadStatusForm" method="post">
            @csrf
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <small class="text-danger fst-italic">* (Star) Marks Fields Are Mandatory</small>
            </div>
    
            <div class="mb-5">
            <label for="leadStatus" class="form-label fw-bold">Update Lead Status <span class="text-danger">*</span></label>
            <select id="leadStatus" name="leadStatus" class="select2 form-select border-primary text-primary" required>
                <option value="" selected disabled>Select Status</option>
                @foreach($ChildParentStatusArr as $leadstatus)
                    <option value="{{$leadstatus['name']}}" data-parent_status_id="{{$leadstatus['parent_status_id']}}" data-child_status_id="{{$leadstatus['child_status_id']}}">{{$leadstatus['name']}}</option>
                @endforeach
            </select>
            </div>
    
            <div class="row g-3 mb-2">
            <div class="col-md-6 mb-3">
                <label for="nextFollowUpDate" class="form-label">Next Follow Up Date <span class="text-danger">*</span></label>
                <input type="date" id="nextFollowUpDate" name="nextFollowUpDate" class="form-control border-primary text-primary" required/>
            </div>
            <div class="col-md-6 mb-3">
                <label for="nextFollowUpTime" class="form-label">Next Follow Up Time <span class="text-danger">*</span></label>
                <input type="time" id="nextFollowUpTime" name="nextFollowUpTime" class="form-control border-primary text-primary" required/>
            </div>
            </div>

            <div class="row g-3 mb-1">
            <div class="col-md-6 mb-3">
                <label for="callPurpose" class="form-label fw-bold">Call Purpose <span class="text-danger">*</span></label>
                <select id="callPurpose" name="callPurpose" class="select2 form-select border-primary text-primary" required>
                    <option value="" selected disabled>Select Call Purpose</option>
                    @foreach($purposeArr as $callpurpose)
                        <option value="{{$callpurpose['id']}}">{{$callpurpose['name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="mood" class="form-label fw-bold">Mood <span class="text-danger">*</span></label>
                <select id="mood" name="mood" class="select2 form-select border-primary text-primary" required>
                    <option value="" selected disabled>Select Mood</option>
                    @foreach($moodArr as $mood)
                        <option value="{{$mood['id']}}"  @if($mood['name'] == 'Neutral') selected @endif >{{$mood['emoji']}} {{$mood['name']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="feedbackTag" class="form-label fw-bold">Feedback Tags </label>
                <select id="feedbackTag" name="feedbackTag[]" class="select2 form-select border-primary text-primary" multiple>
                    @foreach($feedbackTagArr as $feedbacktag)
                        <option value="{{$feedbacktag['id']}}">{{$feedbacktag['name']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    
        <div class="mb-3">
        <label for="specialComment" class="form-label">Special Comment</label>
        <textarea id="specialComment" name="specialComment" class="form-control border-primary text-primary" rows="3" placeholder="Special Comment"></textarea>
        </div>
    
            <button type="submit" class="btn btn-outline-dark btn-sm">Save Changes</button>
        </form>
        </div>

    </div>
    
    <!-- Right info side -->
    <div class="col-md-6">
        <div class="card" style="height: 716px; overflow-y: auto;">
        <div class="leadHistoryContainer card-body p-1">

        @if(!empty($leadHistoryArr) && count($leadHistoryArr) > 0)
            @foreach($leadHistoryArr as $leadHistoryRow)
                @php
                    $statusBadgesHTML = '';

                    if (!empty($leadHistoryRow['parentStatus']['background_color']) && !empty($leadHistoryRow['parentStatus']['font_color']) && !empty($leadHistoryRow['parentStatus']['name'])) {
                        $statusBadgesHTML .= '<span class="badge bg-glow me-1 mt-1 ms-1" style="background-color: ' . e($leadHistoryRow['parentStatus']['background_color']) . '; color: ' . e($leadHistoryRow['parentStatus']['font_color']) . '; font-size: 11px;">' . e($leadHistoryRow['parentStatus']['name']) . '</span>';
                    }

                    if (!empty($leadHistoryRow['childStatus']['background_color']) && !empty($leadHistoryRow['childStatus']['font_color']) && !empty($leadHistoryRow['childStatus']['name'])) {
                        $statusBadgesHTML .= '<span class="badge bg-glow me-1 mt-1" style="background-color: ' . e($leadHistoryRow['childStatus']['background_color']) . '; color: ' . e($leadHistoryRow['childStatus']['font_color']) . '; font-size: 11px;">' . e($leadHistoryRow['childStatus']['name']) . '</span>';
                    }
                @endphp

                <div class="bg-label-success rounded p-3 mb-3">
                    <div class="row align-items-center">
                        @if(!empty($leadHistoryRow['last_call']))
                            <div class="col">
                                <div class="d-flex align-items-center small">
                                    <span class="badge badge-center rounded-pill bg-white text-primary">
                                        <i class="fa-solid fa-headset"></i>
                                    </span>
                                    <span class="text-primary ms-1" style="font-size: 11px;">
                                        Last Call: <strong>{{ $leadHistoryRow['last_call'] }}</strong>
                                    </span>
                                </div>
                            </div>
                        @endif

                        @if(!empty($leadHistoryRow['assigned_telecaller_name']))
                            <div class="col-auto text-end">
                                <span class="fw-semibold text-primary" style="font-size: 12px;">
                                    {{ $leadHistoryRow['assigned_telecaller_name'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($leadHistoryRow['purpose_name']))
                        <div class="small mt-2">
                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                <i class="fa-solid fa-note-sticky"></i>
                            </span>
                            <span class="ms-1 text-primary" style="font-size: 11px;">
                                Purpose: <strong>{{ $leadHistoryRow['purpose_name'] }}</strong>
                            </span>
                        </div>
                    @endif

                    @if(!empty($leadHistoryRow['campaign_type_name']) && !empty($leadHistoryRow['campaign_name']))
                        <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                <i class="fa-solid fa-bullhorn"></i>
                            </span>
                            <span class="badge bg-label-dark bg-glow me-1 mt-1 ms-1" style="font-size: 11px;">{{ $leadHistoryRow['campaign_type_name'] }}</span>
                            <span class="badge bg-label-dark bg-glow me-1 mt-1" style="font-size: 11px;">{{ $leadHistoryRow['campaign_name'] }}</span>
                        </div>
                    @endif

                    @if($statusBadgesHTML)
                        <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                <i class="fa-solid fa-circle-info"></i>
                            </span>
                            {!! $statusBadgesHTML !!}
                        </div>
                    @endif

                    @if(!empty($leadHistoryRow['comment']))
                        <div class="small mt-2">
                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                <i class="fa-solid fa-comment-dots"></i>
                            </span>
                            <span class="ms-1" style="font-size: 11px;">
                                Comment: <strong>{{ $leadHistoryRow['comment'] }}</strong>
                            </span>
                        </div>
                    @endif

                    @if(!empty($leadHistoryRow['mood']['emoji']) && !empty($leadHistoryRow['mood']['name']) && !empty($leadHistoryRow['mood']['color']))
                        <div class="small mt-2">
                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                {{ $leadHistoryRow['mood']['emoji'] }}
                            </span>
                            <span class="ms-1 badge rounded-pill bg-white bg-glow" style="color: {{ $leadHistoryRow['mood']['color'] }}; font-size: 11px;">
                                <strong>{{ $leadHistoryRow['mood']['name'] }}</strong>
                            </span>
                        </div>
                    @endif

                    @if(!empty($leadHistoryRow['feedbackTagNameArr']) && is_array($leadHistoryRow['feedbackTagNameArr']))
                        <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                            <span class="badge badge-center rounded-pill bg-white text-primary me-1">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </span>
                            @foreach($leadHistoryRow['feedbackTagNameArr'] as $tag)
                                <span class="badge bg-glow rounded-pill bg-dark text-white me-1 mt-1" style="font-size: 11px;">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($leadHistoryRow['next_followup_date']) || !empty($leadHistoryRow['next_followup_time']))
                        <div class="text-secondary d-flex align-items-center small mt-1">
                            <span class="badge badge-center rounded-pill bg-white text-primary">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </span>
                            <span class="ms-1" style="font-size: 11px;">
                                Next Schedule: <strong>{{ $leadHistoryRow['next_followup_date'] ?? '' }} {{ $leadHistoryRow['next_followup_time'] ?? '' }}</strong>
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="d-flex justify-content-center align-items-center" style="height: 100%; min-height: 150px;">
                <p class="fw-semibold text-danger m-0" style="font-size: 12px;">No Activity Found.</p>
            </div>
        @endif

            
        </div>
        </div>
    </div>
    
</div>
