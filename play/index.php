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
      "reason" => "Pid not specified",
    ));
    exit();
  } else if( !isset($_GET['shot']) || strlen($_GET['strategy']) === 0) {
    echo json_encode(array(
      "response" => false,
      "reason" => "Shot not specified",
    ));
    exit();
  }

  ////////////////////////////
  // SET UP INITAL DATABASE //
  ////////////////////////////
  require_once('../common/flat.php');
  require_once('../common/common.php');
  require_once('../common/shots.php');
  $setup = new check();
  $setup->run();
  $db = new flat('../data');

  /////////////////////////////
  // Check if pid is correct //
  /////////////////////////////
  $game_data = $db->doc('games')->find($_GET['pid'], 'pid');
  if( sizeof($game_data) === 0 ) { // Was a game found?
    echo json_encode(array(
      "response" => false,
      "reason" => "Unknown pid",
    ));
    exit();
  }

  /////////////////////////
  // setup data variable //
  /////////////////////////
  $db->doc('setting');
  /**
   *   The settings data retrieved from the database, contains the following keys:
   *   "size", "strategies", "ships", "id"
   *   @var array
   */
  $data = $db->find("10", "size");
  $data['strategies'] = json_decode($data['strategies']); // json decode needs to be done to turn the strings into actuall arrays
  $data['ships'] = json_decode($data['ships']);
