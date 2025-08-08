<?php
use App\Helpers\Helper;
?>
<!doctype html>
<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?=config('constants.admin_assets_url')?>assets/"
  data-template="vertical-menu-template-no-customizer"
  data-style="light">
  <head>
    <?=$head?>
  </head>
  <body>
    <!-- Content -->
      <div class="authentication-wrapper authentication-cover">
        <!-- Logo -->
        <a href="javascript:void(0);" class="app-brand auth-cover-brand">
          <span class="app-brand-text demo text-heading fw-bold"><?=Helper::getSettingValue('site_name')?></span>
        </a>
        <!-- /Logo -->
        <div class="authentication-inner row m-0">
          <!-- /Left Text -->
          <div class="d-none d-lg-flex col-lg-8 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
              <img
                src="<?=config('constants.admin_assets_url')?>assets/img/illustrations/auth-login-illustration-light.png"
                alt="auth-login-cover"
                class="my-5 auth-illustration"
                data-app-light-img="illustrations/auth-login-illustration-light.png"
                data-app-dark-img="illustrations/auth-login-illustration-dark.png" />

              <img
                src="<?=config('constants.admin_assets_url')?>assets/img/illustrations/bg-shape-image-light.png"
                alt="auth-login-cover"
                class="platform-bg"
                data-app-light-img="illustrations/bg-shape-image-light.png"
                data-app-dark-img="illustrations/bg-shape-image-dark.png" />
            </div>
          </div>
          <!-- /Left Text -->

          <!-- Login -->
          <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
            <?=(($page_content)?$page_content->page_content:'')?>
          </div>
          <!-- /Login -->
        </div>
      </div>
    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/popper/popper.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/js/bootstrap.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/hammer/hammer.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/i18n/i18n.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/vendor/libs/@form-validation/auto-focus.js"></script>

    <!-- Main JS -->
    <script src="<?=config('constants.admin_assets_url')?>assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="<?=config('constants.admin_assets_url')?>assets/js/pages-auth.js"></script>
    <script src="<?=config('constants.admin_assets_url')?>assets/js/pages-auth-two-steps.js"></script>
    <script type="text/javascript">
      $(function(){
        $('.autohide').delay(5000).fadeOut('slow');
      });
    </script>
  </body>
</html>
