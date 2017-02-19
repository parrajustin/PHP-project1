<?php
require_once('../common/flat.php');

/**
 *   Sets up the environment by adding the base values into the flat files
 */
class check {
  /**
   *   setsup the flat file databases
   *   @method run
   */
  public static function run() {
    $SIZE = 10;
    $AVAILABLE_STRATEGIES = array(
      "Smart",
      "Random",
      "Sweep"
    );
    $AVAILABLE_SHIPS = array(
      array("name" => "Aircraft carrier", "size" => 5),
      array("name" => "Battleship", "size" => 4),
      array("name" => "Frigate", "size" => 3),
      array("name" => "Submarine", "size" => 3),
      array("name" => "Minesweeper", "size" => 2),
    );

    $db = new flat('../data');
    if( $db->doc('setting')->count() == 0 )
      $db->insert(array("size" => $SIZE,"strategies" => json_encode($AVAILABLE_STRATEGIES),"ships" => json_encode($AVAILABLE_SHIPS),));

    if( $db->doc('games')->count() == 0 ) {
      $db->insert(array(
        "pid" => 0,
        "strategy" => "Random",
        "player" => json_encode(array(
          array("name" => "Aircraft carrier", "row" => 5, "col" => 2, "dir" => true),
          array("name" => "Battleship", "row" => 4, "col" => 2, "dir" => true),
          array("name" => "Frigate", "row" => 3, "col" => 2, "dir" => true),
          array("name" => "Submarine", "row" => 3, "col" => 2, "dir" => true),
          array("name" => "Minesweeper", "row" => 2, "col" => 2, "dir" => true),
        )),
        "computer" => json_encode(array(
          array("name" => "Aircraft carrier", "row" => 5, "col" => 2, "dir" => true),
          array("name" => "Battleship", "row" => 4, "col" => 2, "dir" => true),
          array("name" => "Frigate", "row" => 3, "col" => 2, "dir" => true),
          array("name" => "Submarine", "row" => 3, "col" => 2, "dir" => true),
          array("name" => "Minesweeper", "row" => 2, "col" => 2, "dir" => true),
        )),
        "computer_shots" => json_encode(array()),
        "player_shots" => json_encode(array()),
        "gameOver" => false,
        "lastShot" => "-1,-1",
      ));
    }

  }
}

class game {

}
?>
