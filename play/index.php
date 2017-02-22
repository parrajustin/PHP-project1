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
if( !isset($_GET['pid']) || strlen($_GET['pid']) === 0) { // first check if there is a pid
  echo json_encode(array(
    "response" => false,
    "reason" => "Pid not specified",
  ));
  exit();
} else if( !isset($_GET['shot']) || strlen($_GET['shot']) <= 2) { // was there a get for shots?
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
require_once('shot.php');
$game = new game(); // game object from common/common

/////////////////////////////
// Check if pid is correct //
/////////////////////////////
$game->set_pid($_GET['pid']); // setup the game object to find the current game
if( sizeof($game->get_game()) === 0 ) { // Was a game found?
  echo json_encode(array(
    "response" => false,
    "reason" => "Unknown pid",
  ));
  exit();
}
if( $game->game_over() ) { // is the game already over?
  echo json_encode(array(
    "response" => false,
    "reason" => "Game already done!",
  ));
  exit();
}

//////////////////////////////////////////
// Now we need to check the shot itself //
//////////////////////////////////////////
$shot_break_down = explode(',', $_GET['shot']);
if( sizeof($shot_break_down) >= 3 || sizeof($shot_break_down) <= 1) { // are there two elements seprated by a comma?
  echo json_encode(array(
    "response" => false,
    "reason" => "Shot not well-formed",
  ));
  exit();
}
$shot_col = intval($shot_break_down[0]); // shot col = x
$shot_row = intval($shot_break_down[1]); // shot row = y

if( $shot_col <= 0 || $shot_col > $game->get_board_size() || $shot_row <= 0 || $shot_row > $game->get_board_size() ) { // are the shots within the range of the board
  echo json_encode(array(
    "response" => false,
    "reason" => "Invalid shot position, $shot_col,$shot_row",
  ));
  exit();
}

////////////////////////////////////
// Check if the shot hit anything //
////////////////////////////////////
$shot_board = new shot_check($game); // setup shot_board checker
$out_player = $shot_board->check($shot_col, $shot_row, 1); //check if this shot is acceptable and get the data from it
if( is_null($out) ) { // if null is returned the shot is Invalid
  echo json_encode(array(
    "response" => false,
    "reason" => "Invalid shot position, $shot_col,$shot_row",
  ));
  exit();
}

if( !$out['isWin'] ) { // if the player didn't win have the computer go through its strategy

}
