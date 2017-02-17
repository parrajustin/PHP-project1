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
        "strategies" => json_encode(array("Smart", "Random", "Sweep", "test")),
        "ships" => json_encode(array(
          array("name" => "Aircraft carrier", "size" => 5),
          array("name" => "Battleship", "size" => 4),
          array("name" => "Frigate", "size" => 3),
          array("name" => "Submarine", "size" => 3),
          array("name" => "Minesweeper", "size" => 2),
        )),
      ));
    }
   
  }
}


?>