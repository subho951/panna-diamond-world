<style>
    .eachDetail {
        font-size: 8px !important;
    }
</style>
<div class="modal fade activityReportModal" id="" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog
    @if(!empty($leadHistoryArr)) modal-xl @endif
      modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h5 class="modal-title">Modal title</h5> --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if (!empty($leadHistoryArr))
                <div class="modal-body detailActivityReportModal">
                    <div class="d-flex justify-content-between align-items-center pb-1">
                        
                        <h6 class="modal-title" id=""> <strong> {{ $branch_name }}</strong> </h6>
                        
                        @if (!empty($leadHistoryArr))
                            @if (session('user_data')['role_id'] != 3)
                                <button class="exportAsCSVInDetail btn btn-sm"
                                    @if(!empty($assigned_telecaller_name))
                                        data-csvname="{{ $branch_name }}_{{ $assigned_telecaller_name }}"
                                    @else
                                        data-csvname="{{ $branch_name }}"
                                    @endif
                                    style="border: 1px solid green; background-color: green; color: #FFF;">
                                    <i class="fa-solid fa-file-csv"></i>&nbsp;Export CSV
                                </button>
                            @endif
                        @endif
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="eachDetail">#</th>
                                    <th class="eachDetail">Lead No</th>
                                    <th class="eachDetail">Contact Person Name</th>
                                    <th class="eachDetail">Phone</th>
                                    <th class="eachDetail">Campaign Type</th>
                                    <th class="eachDetail">Campaign</th>
                                    <th class="eachDetail">Telecaller</th>
                                    {{-- <th>Parent Status</th> --}}
                                    <th class="eachDetail">Child Status</th>
                                    <th class="eachDetail">Last Call To Lead</th>
                                    <th class="eachDetail">Comment</th>
                                    <th class="eachDetail">Next Schedule</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                {{-- @dd($leadHistoryArr) --}}

                                @foreach ($leadHistoryArr as $leadHistoryRow)

                                    {{-- @dd($leadHistoryRow) --}}

                                    @php
                                        $statusBadgesHTML_Parent = '';
                                        $statusBadgesHTML_Child = '';

                                        if (
                                            !empty($leadHistoryRow['parentStatus']['background_color']) &&
                                            !empty($leadHistoryRow['parentStatus']['font_color']) &&
                                            !empty($leadHistoryRow['parentStatus']['name'])
                                        ) {
                                            $statusBadgesHTML_Parent .=
                                                '<span class="eachDetail badge bg-glow" style="background-color: ' .
                                                e($leadHistoryRow['parentStatus']['background_color']) .
                                                '; color: ' .
                                                e($leadHistoryRow['parentStatus']['font_color']) .
                                                '; ">' .
                                                e($leadHistoryRow['parentStatus']['name']) .
                                                '</span>';
                                        }

                                        if (
                                            !empty($leadHistoryRow['childStatus']['background_color']) &&
                                            !empty($leadHistoryRow['childStatus']['font_color']) &&
                                            !empty($leadHistoryRow['childStatus']['name'])
                                        ) {
                                            $statusBadgesHTML_Child .=
                                                '<span class="eachDetail badge bg-glow" style="background-color: ' .
                                                e($leadHistoryRow['childStatus']['background_color']) .
                                                '; color: ' .
                                                e($leadHistoryRow['childStatus']['font_color']) .
                                                '; ">' .
                                                e($leadHistoryRow['childStatus']['name']) .
                                                '</span>';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="eachDetail">{{ $loop->iteration }}</td>
                                        <td><span
                                                class="eachDetail badge bg-label-info rounded-pill">{{ $leadHistoryRow["lead_sl_no"] }}</span>
                                        </td>
                                        <td><span class="eachDetail fw-bold text-primary"> {{ $leadHistoryRow["contact-person-name"] }}
                                            </span></td>
                                        <td><span class="eachDetail fw-bold text-primary"> {{ $leadHistoryRow["phone"] }} </span></td>
                                        <td><span
                                                class="eachDetail badge bg-label-primary">{{ $leadHistoryRow['campaign_type_name'] }}</span>
                                        </td>
                                        <td><span class="eachDetail badge bg-label-primary">{{ $leadHistoryRow['campaign_name'] }}</span>
                                        </td>

                                        <td><span class="eachDetail fw-bold text-info">{{ $leadHistoryRow["assigned_telecaller_name"] }}</span></td>

                                        {{-- <td>{!! $statusBadgesHTML_Parent !!}</td> --}}
                                        <td>{!! $statusBadgesHTML_Child !!}</td>
                                        <td><span class="eachDetail text-warning">{{ $leadHistoryRow['last_call'] }}</span></td>
                                        <td><span class="eachDetail text-success">{{ $leadHistoryRow['comment'] }}</span></td>
                                        <td>
                                            <span class="eachDetail text-primary">
                                                {{ $leadHistoryRow['next_followup_date'] ?? '' }}
                                                {{ $leadHistoryRow['next_followup_time'] ?? '' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" class="btn btn-primary">Save changes</button> -->
                </div>
            @else
                <div class="d-flex justify-content-center align-items-center"
                    style="height: 100%; min-height: 200px; padding-bottom: 24px;">
                    <p class="fw-semibold text-danger m-0" style="font-size: 12px;">No records available</p>
                </div>
            @endif
        </div>
    </div>
</div>