<?php
/**
 *   Sweep class, should just shoot in a sweeping pattern
*/
class Sweep {
	/**
	 *    Holds the shots fired in a key value pairs with "2,3" => "1" where 2 = x, 3 = y and 1 = computer fired here
	 *   @var [type]
	 */
	private $shots = Null;
	/**
	 *   The game object from common
	 *   @var game
	 */
	private $game = Null;
	/**
	 *   Sets up this swep class
	 *   @method __construct
	 *   @param  array      $game_arry the data from the current game
	 */

	public function __construct($game) {
		$this->game = $game;
		$this->shots = $game->get_computer_shots();
	}

	/**
	 *   Returns where the next shot should be fired
	 *   @method nextShot
	 *   @return string   x,y coordinates where the computer should fire next
	 */
	public function nextShot() {
		//Check if the shots array is empty//
		//If it is add the initial shot at 1,1 and return it//
		if( !$this->game->shot_exists(1, 1, 0) ){
			return array(1,1);
		}
		//Get the last shot from the shot array//
		end($this->shots);
		$key = key($this->shots);
		list($col,$row)=explode(',', $key);
		//Generate the next shot using the last shot coordinates//
		//If the y coordinate is at the end of the board set it to 1 and increment the x coordinate//
		if ($row==$this->game->get_board_size()){
			$col++;
			$row=1;
			//Increment the y coordinate untill it reaches the end of the board///
		}else {
			$row++;
		}
		return array($col, $row);
	}
}
?>
