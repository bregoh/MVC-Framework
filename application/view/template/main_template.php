<?php
  self::checkSession();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $this->pageTitle ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css" />
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
  </head>
  <body>
  
    <?php include(ROOT_DIR .DS. 'application' . DS . 'view' . DS . $this->pageName.'.php') ?>
  </body>
</html>
