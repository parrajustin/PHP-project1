<?php
/**
 *   @author Sebastian A. Urtaza <Sayalaurtaza@miners.utep.edu>
 *   @author Luis Romero <>
 *   @purpose strategy that places shots that sweep the board, or goes from the top left corner to the bottom corner
 */


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
	 *   @param  array      $game the data from the current game
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
		////////////////////////////////////////////////////
		// Does the first shot even exist in the array? //
		////////////////////////////////////////////////////
		if( !$this->game->shot_exists(1, 1, 0) ){
			return array(1,1);
		}

		// Get the last shot from the shot array
		end($this->shots);
		$key = key($this->shots);

		// get the col and row of the last shot
		list($col, $row) = explode(',', $key);

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
