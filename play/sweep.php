<?php
/**
 *   Sweep class, should just shoot in a sweeping pattern
*/
class Sweep {

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
	 *    Holds the shots fired in a key value pairs with "2,3" => "1" where 2 = x, 3 = y and 1 = computer fired here
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
		$this->computer_ships = $game_arry['computer'];
		$this->shots = $game_arry['computer_shots'];
	}

	/**
	 *   Returns where the next shot should be fired
	 *   @method nextShot
	 *   @return string   x,y coordinates where the computer should fire next
	 */
	public function nextShot() {
		//Check if the shots array is empty//
		//If it is add the initial shot at 0,0 and return it//
		if(empty($this->shots)){
			$this->shots['0,0']=1;
			return key($this->shots);
		}
		//Get the last shot from the shot array//
		end($this->shots);
		$key = key($this->shots);
		list($x,$y)=explode(',', $key);
		//Generate the next shot using the last shot coordinates//
		//If the y coordinate is at the end of the board set it to 0 and increment the x coordinate//
		if ($y==10){
			$x++;
			$this->shots[$x.",0"]=1;
			end($this->shots);
			$key = key($this->shots);
			//Increment the y coordinate untill it reaches the end of the board///
		}else {
			$y++;
			$this->shots[$x.",".$y]=1;
			end($this->shots);
			$key = key($this->shots);
		}
		return $key;
	}
}
?>
