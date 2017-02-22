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
    if( $is_player ) // if this is for the player then get the computer's ships
      $ship_storage = $this->game->get_computer_ships();
    else // if this is for the computer get the player's ships
      $ship_storage = $this->game->get_player_ships();

    //////////////////////////////////////
    // Check every ship for a collision //
    //////////////////////////////////////
    /**
     *   Was a ship hit?
     *   @var bool
     */
    $hit_ship = false;
    /**
     *   This is the key of the hit ship for the shipstorage
     *   @var array
     */
    $ship_key = Null;
    foreach ($ship_storage as $key => $value) {
      if( $value['sunk'] !== $value['size'] && $this->shot_collision($value, $col, $row) ) { // ship not already sunk and did the bullet hit the ship?
        $hit_ship = true; // we hit the ship
        $ship_key = $key;
        break; // no longer needed we already iterated through the array
      }
    }

    //////////////////////////
    // Edit the details now //
    //////////////////////////
    /**
     *   Did who ever shot this win?
     *   @var bool
     */
    $is_win_condition = false;
    /**
     *   Edited coordinates of the sunken ship, if one was sunk
     *   @var array
     */
    $ship_sunken_array = array();

    if( !is_null($ship_key) ) { // has a ship been hit?
      $ship_storage[$ship_key]['sunk'] += 1; // increment ship sunk counter
      if( !($ship_storage[$ship_key]['sunk'] === $ship_storage[$ship_key]['size']) ) // has the ship not been destroyed?
        goto skip_hit; // if so skip to after this if statement

      for($i = 0; $i < $ship_storage[$ship_key]['size']; $i++) { // for the sunken ship add the array col/row for the output
        array_push($ship_sunken_array, ($ship_storage[$ship_key]['dir']? $i: 0) + $ship_storage[$ship_key]['col']);
        array_push($ship_sunken_array, (!$ship_storage[$ship_key]['dir']? $i: 0) + $ship_storage[$ship_key]['row']);
      }

      // check if the game is over
      foreach ($ship_storage as $value) {
        if( $value['sunk'] !== $value['size']) { // is this ship not sunken?
          goto skip_hit; // if so skip setting win condidtion to true
        }
      }
      $is_win_condition = true;
    }
    skip_hit: // where the goto statements move to

    /**
     *   We need to update shot array in the database so we need to edit the shots of who ever is made the shot
     *   @var array
     */
    $shot_temp = Null;
    if( $is_player )
      $shot_temp = $this->game->get_player_shots();
    else
      $shot_temp = $this->game->get_computer_shots();
    $shot_temp[$col . "," . $row] = 1; // update the shots to include this shot

    /**
     *   The array to update the game table
     *   @var array
     */
    $update_arry = array(
      "gameOver" => $is_win_condition,
      ($is_player? "player_shots" : "computer_shots")=> $shot_temp,
      (!is_null($ship_key)? ($is_player? "computer" : "player") : "a") => $ship_storage,
    );

    ///////////////////////////////////////////////////////////////////////
    // This updates the game database with the update arry defined above //
    ///////////////////////////////////////////////////////////////////////
    $this->game->update_game($this->game->get_pid(), $update_arry);

    ///////////////////////////////////
    // return this shots information //
    ///////////////////////////////////
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
