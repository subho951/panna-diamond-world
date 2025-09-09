@extends('layouts.auth')
@section('title', 'Sign In')
@section('content')
<?php
use App\Helpers\Helper;
?>
<div class="w-px-400 mx-auto mt-12 pt-5">
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
  <h4 class="mb-1">Welcome to <?=Helper::getSettingValue('site_name')?>!</h4>
  <p class="mb-6">Please sign-in to your account and start the adventure</p>
  <form class="mb-6" action="{{ route('signin') }}" method="POST">
    @csrf
    <div class="mb-6">
      <label for="email" class="form-label">Email or Username</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email or username" autofocus />
    </div>
    <div class="mb-6 form-password-toggle">
      <label class="form-label" for="password">Password</label>
      <div class="input-group input-group-merge">
        <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
        <span class="input-group-text cursor-pointer"><i class="fa-solid fa-eye-slash"></i></span>
      </div>
    </div>
    {{-- Hidden input for reCAPTCHA token --}}
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

    <button type="submit" class="btn btn-outline-dark d-grid w-100">Sign in</button>
    <div class="my-8">
      <div class="d-flex justify-content-between">
        <div class="form-check mb-0 ms-2">
          <!-- <input class="form-check-input" type="checkbox" id="remember-me" />
          <label class="form-check-label" for="remember-me"> Remember Me </label> -->
        </div>
        {{-- <a href="{{url('forgot-password')}}">
          <p class="mb-0 text-dark">Forgot Password?</p>
        </a> --}}
      </div>
    </div>
  </form>
  <div class="mb-2 mb-md-0">
    © <script>document.write(new Date().getFullYear())</script>, Developed & maintained by <a href="https://keylines.net/" target="_blank" class="footer-link fw-medium text-dark">Keylines</a>
  </div>
</div>
<script src="https://www.google.com/recaptcha/api.js?render=6Le9GMArAAAAAIjfufBOGrjKyI4kbWU-5l0-fqmw"></script>
<script>
grecaptcha.ready(function() {
    grecaptcha.execute('6Le9GMArAAAAAIjfufBOGrjKyI4kbWU-5l0-fqmw', {action: 'submit'}).then(function(token) {
        // Add the token to your form submission
        document.getElementById('g-recaptcha-response').value = token;
    });
});
</script>

@endsection
