<?php
/**
 *   Random class, should just shoot at random locations
*/
class Random {

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
	 *   Sets up this strategy
	 *   @method __construct
	 *   @param  game      $game      the main game object
	 */
	public function __construct($game) {
		$this->game = $game;
		$this->shots = $game->get_computer_shots();
	}

	/**
	 *   Returns where the next shot should be fired
	 *   @method nextShot
	 *   @return array   the returned shot where [0] = col, [1] = row
	 */
	public function nextShot() {

		$col = rand(1, $this->game->get_board_size());
		$row = rand(1, $this->game->get_board_size());

		//Check if the coordinates are valid//
		$isValid = false;
		while($isValid == false){
			// Check if the shot exists
			if( $this->game->shot_exists($col, $row, 0) ){
				$col = rand(1, $this->game->get_board_size());
				$row = rand(1, $this->game->get_board_size());
				continue;
			}

			//If the shot is valid add it to the shots array and exit the while//
			$isValid = true;
		}

		return array($col, $row);
	}
}
?>
