<?php
/**
 *   @author Justin R. Parra <jrparra2@miners.utep.edu>
 *   @author Luis Romero <>
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
   *   @var array
   */
  private $settings = null;

  /**
   *   Build this ship checker with information from settings
   *   @method __construct
   *   @param  game      $data settings data
   */
  public function __construct($data) {
    $this->settings = $data;
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
   *   Place a ship onto the board checker
   *   @method place
   *   @param  string $ship_name ship name
   *   @param  array $ship_info should contain all ship information x,y,dir
   *   @return bool             true if ship is correctly placed
   */
  public function place($ship_name, $ship_info) {

    // true for horizontal
    // These statements check if the ship goes out of bounds
    if( $ship_info['col'] + ($ship_info['dir']? $ship_info['size'] : 1) - 1 > $this->settings->get_board_size())
      return false;
    if( $ship_info['row'] + (!$ship_info['dir']? $ship_info['size'] : 1) - 1 > $this->settings->get_board_size())
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
