<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Whatsapp Message</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
  
</head>
<body>

<?php if(session('success_message')){?>
    <div class="alert alert-success alert-dismissible autohide" role="alert">
       <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-desktop align-top me-2"></i>Success!</h6>
       <span><?=session('success_message')?></span>
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
       </button>
    </div>
 <?php }?>
 <?php if(session('error_message')){?>
    <div class="alert alert-danger alert-dismissible autohide" role="alert">
       <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-store align-top me-2"></i>Error!</h6>
       <span><?=session('error_message')?></span>
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
       </button>
    </div>
 <?php }?>

<div class="container my-3">
  <div class="row justify-content-center align-items-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
          <h4 class="text-center mb-4">User Details</h4>
          <form method="POST" action="{{ url('wp-message') }}" enctype="multipart/form-data">
            @csrf

              <div class="mb-3">
                  <label for="name" class="form-label">Name <small class="text-danger">*</small></label>
                  <input type="text" id="name" name="name" class="form-control" placeholder="Enter name" required>
              </div>
          
              <div class="mb-3">
                  <label for="whatsappNo" class="form-label">Whatsapp No. <small class="text-danger">*</small></label>
                  <input type="tel" id="whatsappNo" name="whatsappNo" class="form-control" placeholder="Enter whatsapp number"
                  minlength="10" 
                  maxlength="10" 
                  oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10);" 
                  onblur="if(this.value!=='' && this.value.length!==10){ 
                      alert('Please enter a valid whatsapp number !'); 
                      this.value=''; 
                      this.focus(); 
                  }" required>
                  <small class="form-text">Enter 10-digit whatsapp number</small>
              </div>

            <div class="mb-3">
              <label for="wpMessage" class="form-label">Message </label>
              <textarea class="form-control" id="wpMessage" name="wpMessage" rows="2"></textarea>
            </div>

            <div class="mb-3">
              <label for="wpImage" class="form-label">Image </label>
              <input type="file" id="wpImage" name="wpImage" class="form-control" accept="image/*">
           </div>
        
            <div class="d-grid">
                <button type="submit" class="btn btn-success rounded-pill"><i class="fa-brands fa-whatsapp"></i> Send</button>
            </div>
          </form>
        
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
