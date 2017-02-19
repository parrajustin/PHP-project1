<?php
/**
 *   Random class, should just shoot at random locations
 */
class Random {

  /**
   *   The variable that holds the data for the players ships
   *   @var array
   */
  private $player_ships = Null;
  /**
   *   Variable that holds the data for the computer ships
   *   @var array
   */
  private $computer_ships = Null;
  /**
   *    Holds the shots fired in a key value pairs with "2,3" => "0,1" where 2 = x, 3 = y, 0 = human hasn't fired here and 1 = computer fired here
   *   @var [type]
   */
  private $shots = Null;

  /**
   *   Sets up this swep class
   *   @method __construct
   *   @param  array      $game_arry the data from the current game
   */
  public function __construct($game_arry) {
    $this->player_ships = $game_arry['player'];
    $this->$computer_ships = $game_arry['computer'];
    $this->$shots = $game_arry['shots'];
  }

  /**
   *   Returns where the next shot should be fired
   *   @method nextShot
   *   @return string   x,y coordinates where the computer should fire next
   */
  public function nextShot() {
    return "3,4";
  }
}
?>
