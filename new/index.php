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

////////////////////////////
// SET UP INITAL DATABASE //
////////////////////////////
require_once('../common/flat.php');
require_once('../common/common.php');
require_once('ships.php');
$game = new game();

/**
 *   Keeps track of the avaliable ships that can be places
 *   @var array
 */
$ship_names = $game->get_ship_names();

////////////////////////////////////////////////////////
// Does the strategy specified in the _GET even exist //
////////////////////////////////////////////////////////
if( !in_array($_GET['strategy'], $game->get_avaliable_strategies()) ) {
  echo json_encode(array(
    "response" => false,
    "reason" => "Unknown strategy",
  ));
  exit();
}

/////////////////////////////////////////////////
// are there even the correct number of ships? //
/////////////////////////////////////////////////
$get_ships = explode(';', $_GET['ships']);
if( sizeof($get_ships) != sizeof($game->get_avaliable_ship_array()) ) { // are there the correct number of ships
  echo json_encode(array(
    "response" => false,
    "reason" => "Ship deployment not well-formed, Not the correct amount of ships",
  ));
  exit();
}

/**
 *   Contains the player defined ships, used to store into database
 *   @var array
 */
$ship_storage = array();


/***********************************************************************************************
 *   We need to check that every ship statement , 'name,col,row,dir', is correctly formed then *
 *   we have to check if they are placed correctly on the board so that none of the Ships      *
 *   go out of bounds or intersect each other                                                  *
 ***********************************************************************************************/
foreach ($get_ships as $value) {
  $temp = explode(',', $value); // exploded value of the ship statement, each statement is separted by a  ;
  if( sizeof($temp) != 4 ) { // does each ship statment have the correct number of variables name,x,y,dir
    echo json_encode(array(
      "response" => false,
      "reason" => "Ship deployment not well-formed, incorrect number of statements",
    ));
    exit();
  }

  // replace all + in a ship name with a space since htlm doesn't let you send spaces over urls
  str_replace("+", " ", $temp[0]);
  $temp[1] = (int) $temp[1]; // cast the second string into an int
  $temp[2] = (int) $temp[2]; // same as above

  //////////////////////////////////////////////////////////////////
  // We need to make sure that each of the statements are correct //
  //////////////////////////////////////////////////////////////////
  if( !in_array($temp[0], $ship_names) ) { // Does this ship name exist? check it against the name from the $ship_names array
    echo json_encode(array(
      "response" => false,
      "reason" => "Unknown or Duplicate ship name, $temp[0]",
    ));
    exit();
  }
  // is the x cord a number within the range
  if( $temp[1] < 1 || $temp[1] > $game->get_board_size() ) {
    echo json_encode(array(
      "response" => false,
      "reason" => "Invalid ship x position, $temp[1]",
    ));
    exit();
  }
  // is the y cord a number within the range
  if( $temp[2] < 1 || $temp[2] > $game->get_board_size() ) {
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


  $ship_storage[$temp[0]] = array("name" => $temp[0], "col" => $temp[1], "row" => $temp[2], "dir" => $temp[3], "sunk" => 0, "size" => $game->get_ship_size($temp[0]), ); // add this ship to storage to pass on
  /***********************************************************************************
   *   Here on the unset we are deleting the entry from the ship_names array so that *
   *   we don't place a ship with the same name twice                                *
   ***********************************************************************************/
  unset($ship_names[array_search($temp[0], $ship_names)]);
}

/////////////////////////////////////////////////////////////////////
// check all ships to see if any intersect or any go out of bounds //
/////////////////////////////////////////////////////////////////////
$ship_checker = new Ships($game);
foreach ($ship_storage as $key => $value) {
  if( !$ship_checker->place($key, $value) ) { // if any do intersect or go out of bounds return an error
    echo json_encode(array(
      "response" => false,
      "reason" => "Invalid Ship deployments, $key",
    ));
    exit();
  }
}

//////////////////
// PID CREATION //
//////////////////
$pid = $game->create_pid();
//////////////////////
// END PID CREATION //
//////////////////////

/***************************************************************
 *   Here we reset the ship check so that we may use it agian. *
 *   This way we don't waste memory making two ship checkers   *
 ***************************************************************/
$ship_checker->reset();
$comp_ship_storage = array();

foreach ($ship_storage as $key => $value) { // Create a copy of the ship_storage array for the computers
  $comp_ship_storage[$key] = array();

  foreach ($value as $key2 => $value2) {
    $comp_ship_storage[$key][$key2] = $value2;
  }

  // randomize values
  $comp_ship_storage[$key]['col'] = mt_rand(1, $game->get_board_size());
  $comp_ship_storage[$key]['row'] = mt_rand(1, $game->get_board_size());
  $comp_ship_storage[$key]['dir'] = mt_rand(0,1) == 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// We need to now check if the ships are placed correctly, if not ranomize them until they are //
/////////////////////////////////////////////////////////////////////////////////////////////////
foreach ($comp_ship_storage as $key => $value) {
  while(!$ship_checker->place($key, $comp_ship_storage[$key])) {
    $comp_ship_storage[$key]['dir'] = mt_rand(0,1) == 1;
    $comp_ship_storage[$key]['col'] = mt_rand(1, $game->get_board_size() - ($comp_ship_storage[$key]['dir']? $ship_checker->ship_info[$key] - 1: 0));
    $comp_ship_storage[$key]['row'] = mt_rand(1, $game->get_board_size() - (!$comp_ship_storage[$key]['dir']? $ship_checker->ship_info[$key] - 1: 0));
  }
}

//////////////////////////////////////////////////
// create the array to insert into the database //
//////////////////////////////////////////////////
$db_game_insert = array(
  "pid" => $pid,
  "strategy" => $_GET['strategy'],
  "player" => json_encode(array_values($ship_storage)),
  "computer" => json_encode(array_values($comp_ship_storage)),
  "computer_shots" => json_encode(array("-1,-1" => 1)),
  "player_shots" => json_encode(array("-1,-1" => 1)),
  "gameOver" => false,
  "lastShot" => "-1,-1",
);

$game->insert_game($db_game_insert); // insert this game into the database

//////////////////////////////////////////
// finally return the accepted response //
//////////////////////////////////////////
echo json_encode(array(
  "response" => true,
  "pid" => $pid,
));
?>
