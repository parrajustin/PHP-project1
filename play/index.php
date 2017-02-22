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
if( !isset($_GET['pid']) || strlen($_GET['pid']) === 0) {
  echo json_encode(array(
    "response" => false,
    "reason" => "Pid not specified",
  ));
  exit();
} else if( !isset($_GET['shot']) || strlen($_GET['shot']) <= 2) {
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
$game = new game();

/////////////////////////////
// Check if pid is correct //
/////////////////////////////
$game->set_pid($_GET['pid']);
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
$shot_col = intval($shot_break_down[0]);
$shot_row = intval($shot_break_down[1]);

if( $shot_col <= 0 || $shot_col > $game->get_board_size() || $shot_row <= 0 || $shot_row > $game->get_board_size() ) {
  echo json_encode(array(
    "response" => false,
    "reason" => "Invalid shot position, $shot_col,$shot_row",
  ));
  exit();
}

////////////////////////////////////
// Check if the shot hit anything //
////////////////////////////////////
$shot_board = new shot_check($game);
$out = $shot_board->check($shot_col, $shot_row, 1);
if( is_null($out) ) {

}

echo $out;
