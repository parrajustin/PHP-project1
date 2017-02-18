<?php
class check {
  public function __construct() {
    require_once('../common/flat.php');
    $GLOBALS['db'] = new flat('../data');
  }

  public static function run() {
    $db = $GLOBALS['db'];
    if( $db->doc('setting')->count() == 0 ) {
      $db->insert(array(
        "size" => 10,
        "strategies" => json_encode(array("Smart", "Random", "Sweep")),
        "ships" => json_encode(array(
          array("name" => "Aircraft carrier", "size" => 5),
          array("name" => "Battleship", "size" => 4),
          array("name" => "Frigate", "size" => 3),
          array("name" => "Submarine", "size" => 3),
          array("name" => "Minesweeper", "size" => 2),
        )),
      ));
    }
    
    if( $db->doc('games')->count() == 0 ) {
      $db->insert(array(
        "pid" => 0,
        "strategy" => "Random",
        "player" => json_encode(array(
          array("name" => "Aircraft carrier", "x" => 5, "y" => 2, "ori" => true),
          array("name" => "Battleship", "x" => 4, "y" => 2, "ori" => true),
          array("name" => "Frigate", "x" => 3, "y" => 2, "ori" => true),
          array("name" => "Submarine", "x" => 3, "y" => 2, "ori" => true),
          array("name" => "Minesweeper", "x" => 2, "y" => 2, "ori" => true),
        )),
        "computer" => json_encode(array(
          array("name" => "Aircraft carrier", "x" => 5, "y" => 2, "ori" => true),
          array("name" => "Battleship", "x" => 4, "y" => 2, "ori" => true),
          array("name" => "Frigate", "x" => 3, "y" => 2, "ori" => true),
          array("name" => "Submarine", "x" => 3, "y" => 2, "ori" => true),
          array("name" => "Minesweeper", "x" => 2, "y" => 2, "ori" => true),
        )),
        "shots" => json_encode(array(
          "-1,-1" => "0,0"
        )),
      ));
    }
   
  }
}


?>