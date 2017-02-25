<?php
/**
 *   @author Justin R. Parra <jrparra2@miners.utep.edu>
 *   @author Luis Romero <Lgromero2@miners.utep.edu>
 *   @purpose class that is used to check a ships vladility in a faster way, only checking against other valid ships
 */

/**
 *   Checks the placement of the ships
 */
class Ships {
  /**
   *   Stored ship information has col,row,dir,size,sunk
   *   @var array
   */
  private $ships = array();
  /**
   *   settings object
   *   @var game
   */
  private $game = null;

  /**
   *   Build this ship checker with information from settings
   *   @method __construct
   *   @param  game      $data settings data
   */
  public function __construct($data) {
    $this->game = $data;
  }

  /**
   *   Gets rid of all old ships
   *   @method reset
   */
  public function reset() {
    unset($this->ships);
    $this->ships = array();
  }

  /**
   *   Returns an array or rondomly placed ships
   *   @method random_ships
   *   @return array      the array containing the information of the ships
   */
  public function random_ships() {
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // Make sure there are no ships in the ships array so that they don't interfere with these ships //
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    $this->reset();

    // what will be returned
    $ship_storage = array();

    ////////////////////////////////////////////////////
    // randomly place a ship defined in a ships array //
    ////////////////////////////////////////////////////
    foreach ($this->game->get_ship_names() as $value) {
      // add the ship to storeage with random values
      $ship_storage[$value] = array();
      $ship_storage[$value]["name"] = $value;
      $ship_storage[$value]["dir"] = mt_rand(0, 1) == 1;
      $ship_storage[$value]["size"] = $this->game->get_ship_size($value);
      $ship_storage[$value]["sunk"] = 0;

      // we can get rid of a few spots by making sure the ship fits on the game board
      $ship_storage[$value]['col'] = mt_rand(1, $this->game->get_board_size() - ($ship_storage[$value]['dir']? $ship_storage[$value]['size'] - 1: 0));
      $ship_storage[$value]['row'] = mt_rand(1, $this->game->get_board_size() - (!$ship_storage[$value]['dir']? $ship_storage[$value]['size'] - 1: 0));

      while ( !$this->place($ship_storage[$value]) ) { // keep making ships until one fits
        $ship_storage[$value]['dir'] = mt_rand(0,1) == 1;
        $ship_storage[$value]['col'] = mt_rand(1, $this->game->get_board_size() - ($ship_storage[$value]['dir']? $ship_storage[$value]['size'] - 1: 0));
        $ship_storage[$value]['row'] = mt_rand(1, $this->game->get_board_size() - (!$ship_storage[$value]['dir']? $ship_storage[$value]['size'] - 1: 0));
      }
    }

    return $ship_storage;
  }

  /**
   *   Place a ship onto the board checker
   *   @method place
   *   @param  array $ship_info should contain all ship information x,y,dir
   *   @return bool             true if ship is correctly placed
   */
  public function place($ship_info) {

    // true for horizontal
    // These statements check if the ship goes out of bounds
    if( $ship_info['col'] + ($ship_info['dir']? $ship_info['size'] : 1) - 1 > $this->game->get_board_size())
      return false;
    if( $ship_info['row'] + (!$ship_info['dir']? $ship_info['size'] : 1) - 1 > $this->game->get_board_size())
      return false;

    ///////////////////////////////////
    // Checks if any ships intersect //
    ///////////////////////////////////
    foreach ($this->ships as $value) {
      if( $value['col'] < $ship_info['col'] + ($ship_info['dir']? $ship_info['size']: 1) &&
       $value['col'] + ($value['dir']? $value['size']: 1) > $ship_info['col'] &&
       $value['row'] < $ship_info['row'] + (!$ship_info['dir']? $ship_info['size']: 1) &&
       (!$value['dir']? $value['size']: 1) + $value['row'] > $ship_info['row'])
         return false; // will return if any of the ships intersect each other
    }

    $this->ships[] = $ship_info; // add the ship info to the stored ship array
    return true;
  }
}

 ?>
