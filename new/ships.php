<?php

/**
 *   Checks the placement of the ships
 */
class Ships {
  /**
   *   Stored ship information has col,row,dir,size
   *   @var array
   */
  private $ships = array();
  /**
   *   settings object
   *   @var array
   */
  private $settings = null;
  /**
   *   Ship size info
   *   @var array
   */
  public $ship_info = array();

  /**
   *   Build this ship checker with information from settings
   *   @method __construct
   *   @param  game      $data settings data
   */
  public function __construct($data) {
    $this->settings = $data;

    foreach ($data->get_avaliable_ship_array() as $value)
      $this->ship_info[(string) $value->name] = $value->size;
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
    $ship_info['size'] = $this->ship_info[$ship_name];

    // true for horizontal
    // These statements check if the ship goes out of bounds
    if( $ship_info['dir'] && $ship_info['col'] + $ship_info['size'] - 1 > $this->settings->get_board_size())
      return false;
    if( !$ship_info['dir'] && $ship_info['row'] + $ship_info['size'] - 1 > $this->settings->get_board_size())
      return false;

    ///////////////////////////////////
    // Checks if any boats intersect //
    ///////////////////////////////////
    foreach ($this->ships as $value) {
      if( $value['col'] < $ship_info['col'] + ($ship_info['dir']? $ship_info['size']: 1) &&
       $value['col'] + ($value['dir']? $value['size']: 1) > $ship_info['col'] &&
       $value['row'] < $ship_info['row'] + (!$ship_info['dir']? $ship_info['size']: 1) &&
       (!$value['dir']? $value['size']: 1) + $value['row'] > $ship_info['row'])
         return false;
    }

    $this->ships[] = $ship_info;
    return true;
  }
}

 ?>
