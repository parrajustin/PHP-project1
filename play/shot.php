<?php

/**
 *   checks if everything ships
 */
class shot_check {

  /**
   *   The game object
   *   @var game
   */
  private $game = Null;

  /**
   *   creates this shot check class
   *   @method __construct
   *   @param  game      $game the game object
   */
  public function __construct($game) {
    $this->game = $game;
  }

  /**
   *   Checks the shot
   *   @method check
   *   @param  int  $col       the coloumn of the shto
   *   @param  int  $row       the row of the shot
   *   @param  bool $is_player whether this shot check is for the player
   *   @return array             check the shot
   */
  public function check($col, $row, $is_player) {
    if( $this->game->shot_exists($col, $row, $is_player) )
      return Null;

    ////////////////////////////////////////////
    // Store the necessary ships into storage //
    ////////////////////////////////////////////
    $ship_storage = Null;
    if( $is_player )
      $ship_storage = $this->game->get_computer_ships();
    else
      $ship_storage = $this->game->get_player_ships();

    //////////////////////////////////////
    // Check every ship for a collision //
    //////////////////////////////////////
    $hit_ship = false;
    $ship_details = Null;
    $ship_key = Null;
    foreach ($ship_storage as $key => $value) {
      if( $this->shot_collision($value, $col, $row) ) {
        $hit_ship = true; // we hit the ship
        $ship_details = $value;
        $ship_key = $key;
        break;
      }
    }

    // Didn't hit anything so return the array
    if( $hit_ship === false ) {
      return (array(
        "x" => $col,
        "y" => $row,
        "isHit" => $hit_ship,
        "isSunk" => false,
        "isWin" => false,
        "ship" => array()
      ));
    }

    //////////////////////////
    // Edit the details now //
    //////////////////////////
    $ship_storage[$ship_key]['sunk'] += 1;
    $is_win_condition = true;
    $ship_sunken_array = array();
    if( $ship_storage[$ship_key]['sunk'] === $ship_storage[$ship_key]['size'] ) { // the ship has been sunk
      for($i = 0; $i < $ship_storage[$ship_key]['size']; $i++) { // for the sunken ship add the array col/row for the output
        array_push($ship_sunken_array, (!$ship_storage[$ship_key]['dir']? $i: 0) + $ship_storage[$ship_key]['col']);
        array_push($ship_sunken_array, ($ship_storage[$ship_key]['dir']? $i: 0) + $ship_storage[$ship_key]['row']);
      }

      // check if the game is over
      foreach ($ship_storage as $key => $value) {
        if( $value['sunk'] !== $value['size']) {
          $is_win_condition = false;
          break;
        }
      }
    }

    $shot_temp = $this->game->get_player_shots();
    $shot_temp[$col . "," . $row] = 1;

    $update_arry = array(
      "gameOver" => $is_win_condition,
      ($is_player? "player_shots" : "computer_shots") => $shot_temp,
      ($is_player? "computer" : "player") => $ship_storage,
    );

    $this->game->update_game($this->game->get_pid(), $update_arry);

    return (array(
      "x" => $col,
      "y" => $row,
      "isHit" => $hit_ship,
      "isSunk" => $ship_storage[$ship_key]['sunk'] === $ship_storage[$ship_key]['size'],
      "isWin" => $is_win_condition,
      "ship" => $ship_sunken_array
    ));
  }

  /**
   *   Did this shot collide with the boat
   *   @method shot_collision
   *   @param  array         $rect1    the boat array
   *   @param  int         $shot_col the column of the shot
   *   @param  int         $shot_row the row of the shot
   *   @return bool                   did the shot hit the boat?
   */
  private function shot_collision($rect1, $shot_col, $shot_row) {
    if ($shot_col < $rect1['col'] + ($rect1['dir']? $rect1['size']: 1) &&
       $shot_col + 1 > $rect1['col'] &&
       $shot_row < $rect1['row'] + (!$rect1['dir']? $rect1['size']: 1) &&
       1 + $shot_row > $rect1['row']) {
        return true;
    }
    return false;
  }
}

 ?>
