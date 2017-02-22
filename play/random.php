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
	 *    Holds the shots fired in a key value pairs with "2,3" => "0,1" where 2 = x, 3 = y and 1 = computer fired here
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
	 *   @param  array      $game_arry the specific game array
	 */
	public function __construct($game, $game_arry) {
		$this->game = $game;
		$this->shots = $game_arry['computer_shots'];
	}



	/**
	 *   Returns where the next shot should be fired
	 *   @method nextShot
	 *   @return string   x,y coordinates where the computer should fire next
	 */

	public function nextShot() {
		$x=rand(1,$game->get_board_size());
		$y=rand(1,$game->get_board_size());

		//Check if the coordinates are valid//
		$isValid=false;
		while($isValid==false){
			///First check if the shots array is empty//
			//If it is add the shot to the array and exit the while//
			if(empty($this->shots)){
				$this->shots[$x.",".$y]=1;
				break;
			}
			//If the shot already exist generate a second shot and check again//
			if(array_key_exists($x.",".$y,$this->shots) ){
				$x=rand(1,10);
				$y=rand(1,10);
				continue;
			}
			//If the shot is valid add it to the shots array and exit the while//
			$isValid=true;
			$this->shots[$x.",".$y]=1;
		}

		return $x.",".$y;
	}
}
?>
