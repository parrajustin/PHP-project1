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
	 *   Variable that holds all the coordinates for all the ships with the keys being the coordinates and the value being,
	 *   0 if it has not been shot, 1 if it has been shot.
	 *   @var array
	 */
	private $ship_placements=array();
	/**
	 *   Variable that indicates if the next shot should be a hit or miss.
	 *   @var boolean
	 */
	private  $hit=false;
	/**
	 *   Sets up this under34 class
	 *   @method __construct
	 *   @param  array      $game_arry the data from the current game
	 */
	public function __construct($game_arry) {
		$this->player_ships = $game_arry['player'];
		$this->shots = $game_arry['computer_shots'];
		
		//Fill the ship placements array//
		for($ship=0;$ship<count($this->player_ships);$ship++){
			$this->ship_placements[$this->player_ships[$ship]['row'].','.$this->player_ships[$ship]['col']]= 0;
			if ($this->player_ships[$ship]['dir']){
				for($i=1;$i<$this->player_ships[$ship]['size'];$i++){
					$this->ship_placements[$this->player_ships[$ship]['row'].','.($this->player_ships[$ship]['col']+$i)]=0;
				}
			}else {
				for($i=1;$i<$this->player_ships[$ship]['size'];$i++){
					$this->ship_placements[($this->player_ships[$ship]['row']+$i).','.$this->player_ships[$ship]['col']]=0;
				}
			}
		}
	}
	
	/**
	 *   Generates a shot that is guaranteed to hit a ship
	 *   @method hitShot
	 */
	public function hitShot(){
		//Search through the ship_placements array for a valid coordinate//
		reset($this->ship_placements);
		for ($i=0;$i<count($this->ship_placements);$i++){
			if ($this->ship_placements[key($this->ship_placements)]==0){
				$this->shots[key($this->ship_placements)]=1;
				$this->ship_placements[key($this->ship_placements)]=1;
				break;
			}
			next($this->ship_placements);	
		}
	}
	
	/**
	 *   Generates a shot that is guaranteed to miss
	 *   @method missShot
	 */
	public function missShot(){
		$x=rand(1,10);
		$y=rand(1,10);
		
		//Check if the coordinates are valid//
		$isValid=false;
		while($isValid==false){
			
			//If the generated shot already exist or it can hit a ship generate a second shot and check again//
			if(array_key_exists($x.",".$y,$this->shots) ||  array_key_exists($x.",".$y,$this->ship_placements)){
				$x=rand(1,10);
				$y=rand(1,10);
				continue;
			}
			//If the shot is valid add it to the shots array and exit the while//
			$isValid=true;
			$this->shots[$x.",".$y]=1;
		}
	}
	
	/**
	 *   Returns where the next shot should be fired
	 *   @method nextShot
	 *   @return string   x,y coordinates where the computer should fire next
	 */
	public function nextShot() {
		
		if ($this->hit){
			$this->hitShot();
			$this->hit=false;
		}else{
			$this->missShot();
			$this->hit=true;
		}
		
		end($this->shots);
		return key($this->shots);
		
	}
}
?>
