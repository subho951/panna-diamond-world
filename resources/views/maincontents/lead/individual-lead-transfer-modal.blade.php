
@if(!empty($telecallerData))
<div class="row">
    <div class="col mb-4">
        <form method="post" id="individualLeadTransferForm">
            @csrf
            <select id="" class="select2 form-select" name="to_assigned_telecaller_id" data-allow-clear="true">
                <option value="" disable selected>Select Telecaller</option>
                @foreach($telecallerData as $telecaller)
                    <option value="{{ $telecaller['telecaller_id'] }}">{{ $telecaller['telecaller_name'] }}</option>
                @endforeach
            </select>

            @if(count($totalCampaigns) == 1)
                <input type="hidden" name="branchLead_id_Arr[]" value="{{ $totalCampaigns[0]['branchLead_id'] }}">
                <input type="hidden" name="campaignLength" value="{{ count($totalCampaigns) }}">
            @elseif(count($totalCampaigns) > 1)

                <div class="row mt-2">
                    @foreach($totalCampaigns as $campaigns)
                        @if(!empty($campaigns['campaign_type_id']) && !empty($campaigns['campaign_id']))
                            <div class="col-md-6">
                                <div class="form-check d-flex align-items-center mt-2 gap-2">
                                    <input class="form-check-input" type="checkbox"
                                    value="{{ $campaigns['branchLead_id'] }}"
                                    name="branchLead_id_Arr[]"
                                     
                                    id="{{ $loop->iteration }}" />
                                    <label class="form-check-label" for="{{ $loop->iteration }}">
                                        <span class="badge bg-label-primary mb-1"
                                            style="font-size: 11px; text-wrap: auto; text-align: left;">{{ $campaigns['campaign_type_name'] }}</span><br />
                                        <span class="badge bg-label-primary"
                                            style="font-size: 11px; text-wrap: auto; text-align: left;">{{ $campaigns['campaign_name'] }}</span>
                                    </label>
                                </div>
                            </div>
                        @elseif($campaigns['campaign_type_id'] == 0 && $campaigns['campaign_id'] == 0)
                            <div class="col-md-6">
                                <div class="form-check d-flex align-items-center mt-2 gap-2">
                                    <input class="form-check-input" type="checkbox"
                                    value="{{ $campaigns['branchLead_id'] }}"
                                    name="branchLead_id_Arr[]"
                                     
                                    id="{{ $loop->iteration }}" />
                                    <label class="form-check-label" for="{{ $loop->iteration }}">
                                        <span class="badge bg-label-primary"
                                            style="font-size: 11px; text-wrap: auto; text-align: left;">Without Campaign</span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>

            @endif
            <div class="modal-footer mt-3">
                <button type="submit" class="btn btn-outline-dark btn-sm">Save changes</button>
            </div>
        </form>
    </div>
</div>

@else
    <div class="d-flex justify-content-center align-items-center" style="height: 100%; min-height: 200px; padding-bottom: 24px;">
        <p class="fw-semibold text-danger m-0" style="font-size: 12px;">Sorry, No Other Telecallers Were Found in That Branch</p>
    </div>
@endif
