<?php
/**
 *   @author Justin R. Parra <jrparra2@miners.utep.edu>
 *   @author Sebastian A. Urtaza <Sayalaurtaza@miners.utep.edu>
 *   @author Luis Romero <Lgromero2@miners.utep.edu>
 *   @purpose this is the implementation of the new api, it first checks the validity of the inputs; then it creates the game and returns the game pid
 */

/**
 *   Handles the echoing of data
 *   @method return_statement
 *   @param  array           $array the array to echo out
 */
function return_statement($array) {
  echo json_encode($array);
  exit();
}

/////////////////////////////////////////////////////////////////////
// Checks to make sure the get for both ships and strategy are set //
/////////////////////////////////////////////////////////////////////
if( !isset($_GET['strategy']) || strlen($_GET['strategy']) === 0) // is there a strategy set?
  return_statement(array(
    "response" => false,
    "reason" => "Strategy not specified",
  ));

////////////////////////////
// SET UP INITAL DATABASE //
////////////////////////////
require_once('../common/common.php');
require_once('ships.php');
$game = new game();
/**
 *   Will contain the "Ships" class defined in ships.php
 *   @var Ships
 */
$ship_checker = new Ships($game);
/**
 *   Contains the player defined ships, used to store into database
 *   @var array
 */
$ship_storage = array();

/**
 *   Keeps track of the avaliable ships that can be placed
 *   @var array
 */
$ship_names = $game->get_ship_names();

////////////////////////////////////////////////////////
// Does the strategy specified in the _GET even exist //
////////////////////////////////////////////////////////
if( !in_array($_GET['strategy'], $game->get_avaliable_strategies()) ) // is the strategy specified one of the avaliable strategies
  return_statement(array(
    "response" => false,
    "reason" => "Unknown strategy",
  ));


/*************************************************
 *                                               *
 *                                               *
 *   DID THE USER DEFINE ANY SHIPS TO BE PLACED  *
 *                                               *
 *                                               *
 *************************************************/
if( isset($_GET['ships']) ) { // are the ships set and is there something there?
  /////////////////////////////////////////////////
  // are there even the correct number of ships? //
  /////////////////////////////////////////////////
  $get_ships = explode(';', $_GET['ships']);
  if( sizeof($get_ships) != sizeof($game->get_avaliable_ship_array()) ) // are there the correct number of ships
    return_statement(array(
      "response" => false,
      "reason" => "Ship deployment not well-formed, Not the correct amount of ships",
    ));


  /***********************************************************************************************
   *   We need to check that every ship statement , 'name,col,row,dir', is correctly formed then *
   *   we have to check if they are placed correctly on the board so that none of the Ships      *
   *   go out of bounds or intersect each other                                                  *
   ***********************************************************************************************/
  foreach ($get_ships as $value) {
    $temp = explode(',', $value); // exploded value of the ship statement, each statement is separted by a  ;
    if( sizeof($temp) != 4 ) // does each ship statment have the correct number of variables name,x,y,dir
      return_statement(array(
        "response" => false,
        "reason" => "Ship deployment not well-formed, incorrect number of statements",
      ));

    // replace all + in a ship name with a space since htlm doesn't let you send spaces over urls
    str_replace("+", " ", $temp[0]);
    $temp[1] = (int) $temp[1]; // cast the second string into an int
    $temp[2] = (int) $temp[2]; // same as above

    //////////////////////////////////////////////////////////////////
    // We need to make sure that each of the statements are correct //
    //////////////////////////////////////////////////////////////////
    if( !in_array($temp[0], $ship_names) ) // Does this ship name exist? check it against the name from the $ship_names array
      return_statement(array(
        "response" => false,
        "reason" => "Unknown or Duplicate ship name, $temp[0]",
      ));

    // is the x cord a number within the range
    if( $temp[1] < 1 || $temp[1] > $game->get_board_size() )
      return_statement(array(
        "response" => false,
        "reason" => "Invalid ship x position, $temp[1]",
      ));

    // is the y cord a number within the range
    if( $temp[2] < 1 || $temp[2] > $game->get_board_size() )
      return_statement(array(
        "response" => false,
        "reason" => "Invalid ship y position, $temp[2]",
      ));

    // is the dir either "true" or "false"
    if( strtolower($temp[3]) === "false" )
      $temp[3] = false;
    else if( strtolower($temp[3]) === "true" )
      $temp[3] = true;
    else
      return_statement(array(
        "response" => false,
        "reason" => "Invalid ship direction, $temp[3]",
      ));


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
  foreach ($ship_storage as $key => $value) {
    if( !$ship_checker->place($value) ) // if any do intersect or go out of bounds return an error
      return_statement(array(
        "response" => false,
        "reason" => "Invalid Ship deployments, $key",
      ));
  }
}
/***************************
 *                         *
 *                         *
 *  WERE NO SHIPS DEFINED? *
 *                         *
 *                         *
 ***************************/
else
   $ship_storage = $ship_checker->random_ships();

//////////////////
// PID CREATION //
//////////////////
$pid = $game->create_pid();
//////////////////////
// END PID CREATION //
//////////////////////

/////////////////////////////////////////////
// Generate random ships for the computer  //
/////////////////////////////////////////////
$comp_ship_storage = $ship_checker->random_ships();

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
return_statement(array(
  "response" => true,
  "pid" => $pid,
));
?>
