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

  if( !isset($_GET['ships']) || strlen($_GET['ships']) === 0) {
    echo json_encode(array(
      "response" => false,
      "reason" => "No ships specified",
    ));
    exit();
  } else if( !isset($_GET['strategy']) || strlen($_GET['strategy']) === 0) {
    echo json_encode(array(
      "response" => false,
      "reason" => "Strategy not specified",
    ));
    exit();
  }

  // SETUP DATABASE
  require_once('../common/flat.php');
  require_once('../common/common.php');
  require_once('ships.php');
  $setup = new check();
  $setup->run();
  $db = new flat('../data');
  // ===== END SETUP

  // setup data variable
  $db->doc('setting');
  $data = $db->find("10", "size");
  $data['strategies'] = json_decode($data['strategies']);
  $data['ships'] = json_decode($data['ships']);

  $ship_names = array(); // to keep track of avaliable ships
  foreach ($data['ships'] as $value) {
    $ship_names[] = (string) $value->name;
  }
  // ===== END data setup

  // does the strategy even exist
  if( !in_array($_GET['strategy'], $data['strategies']) ) {
    echo json_encode(array(
      "response" => false,
      "reason" => "Unknown strategy",
    ));
    exit();
  }

  // check ship playements
  $get_ships = explode(';', $_GET['ships']);
  if( sizeof($get_ships) != sizeof($data['ships']) ) { // are there the correct number of ships
    echo json_encode(array(
      "response" => false,
      "reason" => "Ship deployment not well-formed, Not the correct amount of ships",
    ));
    exit();
  }
  $ship_storage = array();

  // check each ship statement
  foreach ($get_ships as $value) {
    $temp = explode(',', $value);
    if( sizeof($temp) != 4 ) { // does each ship statment have the correct number of variables name,x,y,dir
      echo json_encode(array(
        "response" => false,
        "reason" => "Ship deployment not well-formed, incorrect number of statements",
      ));
      exit();
    }

    str_replace("+", " ", $temp[0]);
    $temp[1] = (int) $temp[1];
    $temp[2] = (int) $temp[2];

    // =====
    // checks to make sure each of the values are correct
    // =====
    // does this ship even exist
    if( !in_array($temp[0], $ship_names) ) { // does each ship statment have the correct number of variables name,x,y,dir
      echo json_encode(array(
        "response" => false,
        "reason" => "Unknown or Duplicate ship name, $temp[0]",
      ));
      exit();
    }
    // is the x cord a number within the range
    if( $temp[1] < 1 || $temp[1] > $data['size'] ) { // does each ship statment have the correct number of variables name,x,y,dir
      echo json_encode(array(
        "response" => false,
        "reason" => "Invalid ship x position, $temp[1]",
      ));
      exit();
    }
    // is the y cord a number within the range
    if( $temp[2] < 1 || $temp[2] > $data['size'] ) { // does each ship statment have the correct number of variables name,x,y,dir
      echo json_encode(array(
        "response" => false,
        "reason" => "Invalid ship y position, $temp[2]",
      ));
      exit();
    }
    // is the dir either "true" or "false"
    if( strtolower($temp[3]) === "false" )
      $temp[3] = false;
    else if( strtolower($temp[3]) === "true" )
      $temp[3] = true;
    else {
      echo json_encode(array(
        "response" => false,
        "reason" => "Invalid ship direction, $temp[3]",
      ));
      exit();
    }

    $ship_storage[$temp[0]] = array("name" => $temp[0], "col" => $temp[1], "row" => $temp[2], "dir" => $temp[3] ); // add this ship to storage to pass on
    unset($ship_names[array_search($temp[0], $ship_names)]); // get rid of ship in ship names so we can't place it twice
  }

  // check all ships to see if any intersect or any go out of bounds
  $ship_checker = new Ships($data);
  foreach ($ship_storage as $key => $value) {
    if( !$ship_checker->place($key, $value) ) {
      echo json_encode(array(
        "response" => false,
        "reason" => "Invalid Ship deployments, $key",
      ));
      exit();
    }
  }

  $pid = uniqid();
  $db->doc('games'); // set db pointer to the games table
  $pids = ["a"];
  while( sizeof($pids) != 0) $pids = $db->find($pid, "pid"); // make sure this pid is unique

  $ship_checker->reset(); // get rid of checker so that we may make one for the computer's ships
  $comp_ship_storage = array();

  foreach ($ship_storage as $key => $value) {
    $comp_ship_storage[$key] = array();

    foreach ($value as $key2 => $value2) {
      $comp_ship_storage[$key][$key2] = $value2;
    }

    $comp_ship_storage[$key]['col'] = mt_rand(1, $data['size']);
    $comp_ship_storage[$key]['row'] = mt_rand(1, $data['size']);
    $comp_ship_storage[$key]['dir'] = mt_rand(0,1) == 1;
  }
  foreach ($comp_ship_storage as $key => $value) {
    while(!$ship_checker->place($key, $comp_ship_storage[$key])) {
      $comp_ship_storage[$key]['dir'] = mt_rand(0,1) == 1;
      $comp_ship_storage[$key]['col'] = mt_rand(1, $data['size'] - ($comp_ship_storage[$key]['dir']? $ship_checker->ship_info[$key] - 1: 0));
      $comp_ship_storage[$key]['row'] = mt_rand(1, $data['size'] - (!$comp_ship_storage[$key]['dir']? $ship_checker->ship_info[$key] - 1: 0));
    }
  }

  // create the array to insert into the database
  $db_game_insert = array(
    "pid" => $pid,
    "strategy" => $_GET['strategy'],
    "player" => json_encode(array_values($ship_storage)),
    "computer" => json_encode(array_values($comp_ship_storage)),
    "shots" => "[]",
  );

  $db->insert($db_game_insert); // insert this game into the database

  echo json_encode(array(
    "response" => true,
    "pid" => $pid,
  ));
?>
