$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
    
    let baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const base_url = document.querySelector('meta[name="baseurl"]').getAttribute('content');


    // handling call modal
    // $(document).on('click', '.callButton', function(){
    //     console.log('call modal launched');
    // });


});
