<?php
/**
 *   Will sink all the player ships in 34 turns.
*/
class Under34 {

	/**
	 *   The variable that holds the data for the players ships
	 *   @var array
	 */
	private $player_ships = Null;

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
	 *   Sets up this under34 class
	 *   @method __construct
	 *   @param  array      $game_arry the data from the current game
	 */
	public function __construct($game) {
		$this->game = $game;
		$this->shots = $game->get_computer_shots();
		$this->player_ships = $game->get_player_ships();

	}

	/**
	 *   Generates a shot that is guaranteed to hit a ship
	 *   @method hitShot
	 */
	public function hitShot(){
		$row=$col=$dir=null;
		//Get the coordinates and direction of the first available ship that is alive//
		foreach ($this->player_ships as $key => $value) {
			if( $value['sunk'] !== $value['size'] ) {
				$row=$value['row'];
				$col=$value['col'];
				$dir=$value['dir'];
				break;
			}
		}

		//Check the past shots to see where the next shot for that ship should be//
		$isValid=false;
		while($isValid == false){
			if( $this->game->shot_exists($col, $row, 0) ){
				if($dir){
					$col++;
				}else{
					$row++;
				}
				continue;
			}
			$isValid = true;
		}
		return array($col, $row);
	}

	/**
	 *   Generates a shot that is guaranteed to miss
	 *   @method missShot
	 */
	public function missShot(){
		$col = rand(1, $this->game->get_board_size());
		$row = rand(1, $this->game->get_board_size());

		//Check if the coordinates are valid//
		$isValid=false;
		while($isValid==false){
				
			//If the generated shot already exist or it can hit a ship generate a second shot and check again//
			if( $this->game->shot_exists($col, $row, 0) ){
				$col = rand(1, $this->game->get_board_size());
				$row = rand(1, $this->game->get_board_size());
				continue;
			}
			foreach ($this->player_ships as $key => $value) {
				if($this->shot_collision($value, $col, $row)){
					$col = rand(1, $this->game->get_board_size());
					$row = rand(1, $this->game->get_board_size());
					continue;
				}
			}
			$isValid=true;
				
		}
		return array($col, $row);
	}

	/**
	 *   Returns where the next shot should be fired
	 *   @method nextShot
	 *   @return string   x,y coordinates where the computer should fire next
	 */
	public function nextShot() {
		//Check if this is the first shot since the first shot will always be a miss//
		end($this->shots);
		if (key($this->shots)=="-1,-1"){
			return $this->missShot();
		}
		//Check if the las shot hit a ship, if it did return a miss shot else return a hit shot//
		list($col,$row)=explode(',', key($this->shots));
		foreach ($this->player_ships as $key => $value) {
			if($this->shot_collision($value, $col, $row)){
				return $this->missShot();
			}
		}
		return $this->hitShot();
	}

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
