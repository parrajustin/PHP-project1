<html>
<head>
</head>
<body>
  <?php
  ini_set('display_errors', 1);
  error_reporting(E_ALL ^ E_NOTICE);
  ?>
</body>
</html>

<?php

  /////////////////////////////////////////////////////////////////////
  // Checks to make sure the get for both ships and strategy are set //
  /////////////////////////////////////////////////////////////////////
  if( !isset($_GET['pid']) || strlen($_GET['ships']) === 0) {
    echo json_encode(array(
      "response" => false,
      "reason" => "No ships specified",
    ));
    exit();
  } else if( !isset($_GET['shot']) || strlen($_GET['strategy']) === 0) {
    echo json_encode(array(
      "response" => false,
      "reason" => "Strategy not specified",
    ));
    exit();
  }

  ////////////////////////////
  // SET UP INITAL DATABASE //
  ////////////////////////////
  require_once('../common/flat.php');
  require_once('../common/common.php');
  $setup = new check();
  $setup->run();
  $db = new flat('../data');

  /////////////////////////
  // setup data variable //
  /////////////////////////
  $db->doc('setting');
