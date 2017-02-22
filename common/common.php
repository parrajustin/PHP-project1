<?php
require_once('../common/flat.php');

/** The class that gives access to the underlying needs of the game */
class game {

  /**
   *   The database object
   *   @var flat
   */
  private $database = null;

  /**
   *   the size of the game board
   *   @var int
   */
  private $SIZE = 10;

  /**
   *   The avaliable strategies
   *   @var array
   */
  private $AVAILABLE_STRATEGIES = array(
    "Smart",
    "Random",
    "Sweep"
  );

  /**
   *   The avaliable ships
   *   @var array
   */
  private $AVAILABLE_SHIPS = array(
    array("name" => "Aircraft carrier", "size" => 5),
    array("name" => "Battleship", "size" => 4),
    array("name" => "Frigate", "size" => 3),
    array("name" => "Submarine", "size" => 3),
    array("name" => "Minesweeper", "size" => 2),
  );

  /**
   *   Creates the game class and sets up the game envrionment
   *   @method __construct
   */
  public function __construct() {
    // institate database
    $db = new flat('../data');
    $this->database = $db;

    ////////////////////////////////////
    // ENSURE DEFAULT DATABASE EXISTS //
    ////////////////////////////////////
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
        "shots" => "[]",
        "gameOver" => false,
        "lastShot" => "-1,-1",
      ));

      $this->database->doc('games'); // set db pointer to the games table
    }
    ////////////////////////////////
    // END DEFAULT DATABASE SETUP //
    ////////////////////////////////
  }

  /**
   *   Returns the size of the game board
   *   @method get_board_size
   *   @return int         the size
   */
  public function get_board_size() {
    return $this->SIZE;
  }

  /**
   *   Returns an array of the avaliable strategies
   *   @method get_avaliable_strategies
   *   @return string                   string holding the avaliable strategies
   */
  public function get_avaliable_strategies() {
    return $this->AVAILABLE_STRATEGIES;
  }

  /**
   *   Returns the array of avaliable ships
   *   @method get_avaliable_ship_array
   *   @return array                   array holding all avaliable ships
   */
  public function get_avaliable_ship_array() {
    return $this->AVAILABLE_SHIPS;
  }

  /**
   *   Returns the size of a ship
   *   @method get_ship_size
   *   @param  string        $name name of the ship to get the size of
   *   @return int              integar size
   */
  public function get_ship_size($name = "a") {
    for($i = 0; $i < sizeof($this->AVAILABLE_SHIPS); $i++) {
      if($this->AVAILABLE_SHIPS[$i]['name'] === $name)
        return $this->AVAILABLE_SHIPS[$i]['size'];
    }

    return NULL;
  }

  /**
   *   Returns an array of the ship names
   *   @method get_ship_names
   *   @return array         array of ship names
   */
  public function get_ship_names() {
    $ship_names = array();
    foreach ($this->AVAILABLE_SHIPS as $value) {
      $ship_names[] = $value['name'];
    }

    return $ship_names;
  }

  /**
   *   Creates a unique pid that doesn't exist
   *   @method create_pid
   *   @return string     the pid string
   */
  public function create_pid() {
    $pid = Null; // create the uniq pid
    $pids = ["a"];
    while( sizeof($pids) != 0) {
      $pid = uniqid(); // create the uniq pid
      $pids = $this->database->find($pid, "pid"); // make sure this pid is unique
    }
    return $pid;
  }

  /**
   *   Inserts the array into the game table
   *   @method insert_game
   *   @param  array      $insertee the array to be inserted
   */
  public function insert_game($insertee) {
    $this->database->insert($insertee);
  }
}
?>
