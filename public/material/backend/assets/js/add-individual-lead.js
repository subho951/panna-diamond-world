$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
    $('#telecaller_id').select2({
        // placeholder: "Select Telecaller",
        allowClear: true
    });
    let baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const base_url = document.querySelector('meta[name="baseurl"]').getAttribute('content');
    // fetch telecallers on branch change 
    let branch_id;
    $(document).on('change', '#branch_id', function () {
        branch_id = $(this).val();
        if (Number(branch_id)) {
            $.ajax({
                url: base_url + '/individual-lead/fetch-telecaller',
                type: "POST",
                data: {
                    branch_id: branch_id,
                },
                success: function (res) {
                    $('#telecaller_id').empty();
                    $('#telecaller_id').append(`<option value="" selected disabled>Select Telecaller</option>`);
                    res.forEach(telecaller => {
                        $('#telecaller_id').append(`
                            <option value="${telecaller.id}">${telecaller.first_name} ${telecaller.last_name}</option>
                        `);
                    });

                },
                error: function (err) {
                    console.error('Fetch failed:', err);
                },
            });
        }
    });
    // fetch telecallers on branch change end

    //fetch campaign on Campaign Type change
    let campaign_type_id;
    $(document).on('change', '#campaign_type_id', function () {
        campaign_type_id = $(this).val();
        if (Number(campaign_type_id)) {
            $.ajax({
                url: base_url + '/individual-lead/fetch-campaign',
                type: 'POST',
                data: { campaign_type_id: campaign_type_id },
                success: function (res) {

                    $('#campaign_id').empty();
                    $('#campaign_id').attr('required', true);
                    $('.campaign_star').text('*');
                    $('#campaign_id').append(`<option value="" selected disabled>Select Campaign</option>`);
                    res.forEach(campaign => {
                        $('#campaign_id').append(`<option value="${campaign.id}">${campaign.name}</option>`);
                    });
                },
                error: function (err) {
                    console.error('Fetch failed:', err);
                }
            });
        }
    });
    //fetch campaign on Campaign Type change end

    //fetch state and phone code on country change
    let country_id;
    let selectedOption;
    $(document).on('change', '#country', function(){
        selectedOption = $(this).find('option:selected');
        country_id = selectedOption.data('countryid');
        // console.log(country_id);
        if(Number(country_id))
        {
            $.ajax({
                url: base_url + '/individual-lead/fetch-state',
                type: 'POST',
                data: {country_id : country_id},
                success: function(res)
                {
                    // console.log(res); 
                    $("#state").empty();
                    $("#state").append(`<option value="" selected disabled>Select State</option>`);
                    res.forEach(state => {
                        $("#state").append(`
                            <option value="${state.name}">${state.name}</option>
                        `);
                    });
                },
                error: function (err) {
                    console.error('Fetch failed:', err);
                }
            });

            $.ajax({
                url: base_url + '/individual-lead/fetch-phone-code',
                type: 'POST',
                data: {country_id : country_id},
                success: function(res)
                {
                    // console.log(res); 
                    $("#phone-code").empty();
                    $("#phone-code").append(`<option value="" selected disabled>Select Phone Code</option>`);
                    res.forEach(phone_code => {
                        $("#phone-code").append(`
                            <option value="${phone_code.phone_code}" selected>${phone_code.phone_code}</option>
                        `);
                    });
                },
                error: function (err) {
                    console.error('Fetch failed:', err);
                }
            });

        }
    })
    //fetch state and phone code on country change end

});
