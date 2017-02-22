<?php
/**
 *   Smart class, should actually play well
 */
class Smart {

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
    $this->computer_ships = $game_arry['computer'];
    $this->shots = $game_arry['shots'];
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
  		//If the shot is not empty check if the coordates are equal to a ship position//
  		if(!empty($this->shots)){
  			//this if check if x is equal to the row of the ship if it is true 
  			//add one to x to make a shot next to the previous 
  			if((array_key_exists($x.",".$y,$this->shots )) && (computer_ship['row']==$x) ){
  				$x=$x+1;
  				continue;
  			}
  			//this if check if y is equal to the col of the ship if it is true
  			//add one to y to make a shot next to the previous
  			if((arra_key_exist($x.",".$y,$this->shots)) && (computer_ship['col']==$y)){
  				$y=$y+1;
  				continue;
  			}
  			//If x and y are not equal to a col or row from computer ship
  			//Select a new random x and y
  			else {
  				$x=rand(1,$game->get_board_size());
  				$y=rand(1,$game->get_board_size());
  				continue;
  			}
  		}			
  		//If the shot is valid add it to the shots array and exit the while//
  		$isValid=true;
  		$this->shots[$x.",".$y]=1;
  	}
  	
  	return $x.",".$y;
  	
  }
}
?>
