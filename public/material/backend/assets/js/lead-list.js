$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
    
    let baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const base_url = document.querySelector('meta[name="baseurl"]').getAttribute('content');

    //Pagination
    // document.getElementById('perPageSelect').addEventListener('change', function() {
    //     const url = new URL(window.location.href);
    //     url.searchParams.set('perPage', this.value);
    //     url.searchParams.set('page', 1); // reset to first page
    //     window.location.href = url.toString();
    // });


    // handling call modal
    // $(document).on('click', '.callButton', function(){
    //     console.log('call modal launched');
    // });


});
