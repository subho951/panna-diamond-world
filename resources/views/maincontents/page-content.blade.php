<html>

<head>
  <title><?= (($page_content) ? $page_content->page_name : '') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</head>

<body>
  <div class="container">
    <div class="row">
      <h3 class="mb-3"><?= (($page_content) ? $page_content->page_name : '') ?></h3>
      <div class="col-md-12">
        <?= (($page_content) ? $page_content->page_content : '') ?>
      </div>
    </div>
  </div>
</body>

</html>