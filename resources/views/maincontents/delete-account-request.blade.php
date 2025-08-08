<html>

<head>
    <title>Delete Account Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <div class="row">
            <h3 class="mb-3">Delete Account Request</h3>
            <div class="col-md-12">
                @if(session('success_message'))
                <div class="alert alert-success">
                    {{ session('success_message') }}
                </div>
                @endif

                @if(session('error_message'))
                <div class="alert alert-danger">
                    {{ session('error_message') }}
                </div>
                @endif

                <form method="POST" action="{{ route('delete-account.store') }}" class="w-50 mx-auto mt-4">
                    @csrf

                    <div class="form-group">
                        <label for="entity_name">User Type</label>
                        <input type="text" class="form-control" id="user_type" name="user_type" required>
                    </div>

                    <div class="form-group">
                        <label for="entity_name">Entity Name</label>
                        <input type="text" class="form-control" id="entity_name" name="entity_name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="comments">Comments (optional)</label>
                        <textarea class="form-control" id="comments" name="comments" rows="4"></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Submit Delete Request</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>