<?php
var_dump("POST :",$_GET);
?>

/////////////////////////////////////////////////////////////////////
// Checks to make sure the get for both ships and strategy are set //
/////////////////////////////////////////////////////////////////////
if( !isset($_GET['pid']) || strlen($_GET['ships']) === 0) {
  echo json_encode(array(
    "response" => false,
    "reason" => "Pid not specified",
  ));
  exit();
} else if( !isset($_GET['shot']) || strlen($_GET['strategy']) <= 2) {
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
$game_data['computer'] = json_decode($game_data['computer']);

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

if( $shot_col <= 0 || $shot_col > $data['size'] || $shot_row <= 0 || $shot_row > $data['size'] ) {
  echo json_encode(array(
    "response" => false,
    "reason" => "Invalid shot position, $shot_col,$shot_row",
  ));
  exit();
}

////////////////////////////////////
// Check if the shot hit anything //
////////////////////////////////////
$hit = null; // what the shot hit
for($i = 0; $i < sizeof($game_data['computer']); $i++) {
  if( $shot_col < $game_data['computer'][$i]['col'] + ($game_data['computer'][$i]['dir']? $game_data['computer'][$i]['size']: 1) &&
   $shot_col + ($value['dir']? $value['size']: 1) > $game_data['computer'][$i]['col'] &&
   $shot_row < $game_data['computer'][$i]['row'] + (!$game_data['computer'][$i]['dir']? $game_data['computer'][$i]['size']: 1) &&
   (!$value['dir']? $value['size']: 1) + $shot_row > $game_data['computer'][$i]['row']) {
     $hit = $game_data['computer'][$i];
   }
}
