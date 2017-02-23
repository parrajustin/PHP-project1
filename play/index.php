
<?php
/**
 *   Handles the echoing of data
 *   @method return_statement
 *   @param  array           $array the array to echo out
 */
function return_statement($array) {
  echo json_encode($array);
  exit();
}
// test

/////////////////////////////////////////////////////////////////////
// Checks to make sure the get for both ships and strategy are set //
/////////////////////////////////////////////////////////////////////
if( !isset($_GET['pid']) || strlen($_GET['pid']) === 0) // first check if there is a pid
  return_statement(array(
    "response" => false,
    "reason" => "Pid not specified",
  ));
else if( !isset($_GET['shot']) || strlen($_GET['shot']) <= 2)// was there a get for shots?
  return_statement(array(
    "response" => false,
    "reason" => "Shot not specified",
  ));

////////////////////////////
// SET UP INITAL DATABASE //
////////////////////////////
require_once('../common/common.php');
require_once('shot.php');
require_once('random.php');
$game = new game(); // game object from common/common

/////////////////////////////
// Check if pid is correct //
/////////////////////////////
$game->set_pid($_GET['pid']); // setup the game object to find the current game
if( sizeof($game->get_game()) === 0 ) // Was a game found?
  return_statement(array(
    "response" => false,
    "reason" => "Unknown pid",
  ));
if( $game->game_over() ) // is the game already over?
  return_statement(array(
    "response" => false,
    "reason" => "Game already done!",
  ));

//////////////////////////////////////////
// Now we need to check the shot itself //
//////////////////////////////////////////
$shot_break_down = explode(',', $_GET['shot']);
if( sizeof($shot_break_down) >= 3 || sizeof($shot_break_down) <= 1) // are there two elements seprated by a comma?
  return_statement(array(
    "response" => false,
    "reason" => "Shot not well-formed",
  ));
$shot_col = intval($shot_break_down[0]); // shot col = x
$shot_row = intval($shot_break_down[1]); // shot row = y

if( $shot_col <= 0 || $shot_col > $game->get_board_size() || $shot_row <= 0 || $shot_row > $game->get_board_size() ) // is the shot within the range of the board
  return_statement(array(
    "response" => false,
    "reason" => "Invalid shot position, $shot_col,$shot_row",
  ));

////////////////////////////////////
// Check if the shot hit anything //
////////////////////////////////////
$shot_board = new shot_check($game); // setup shot_board checker
$out_player = $shot_board->check($shot_col, $shot_row, 1); //check if this shot is acceptable and get the data from it
$out_computer = null;
if( is_null($out_player) ) // if null is returned the shot is Invalid
  return_statement(array(
    "response" => false,
    "reason" => "Invalid shot position, $shot_col,$shot_row",
  ));

if( !$out_player['isWin'] ) { // if the player didn't win have the computer go through its strategy
  $strat = new Random($game);
  $returned_shot = Null;

  while( is_null($out_computer) || sizeof($out_computer) === 0) { // if null is returned the shot is Invalid, emergency mesure
    $returned_shot = $strat->nextShot();
    $out_computer = $shot_board->check($returned_shot[0], $returned_shot[1], 0); // check the computer's shot
  }
}

return_statement(array(
  "response" => true,
  "ack_shot" => $out_player,
  "shot" => (is_null($out_computer)? json_decode("{}") : $out_computer),
));
