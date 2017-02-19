<?php
class Borad{
	var $board= array(array(0,0,0,0,0,0,0,0,0,0),array(0,0,0,0,0,0,0,0,0,0),
			array(0,0,0,0,0,0,0,0,0,0),array(0,0,0,0,0,0,0,0,0,0),
			array(0,0,0,0,0,0,0,0,0,0),array(0,0,0,0,0,0,0,0,0,0),
			array(0,0,0,0,0,0,0,0,0,0),array(0,0,0,0,0,0,0,0,0,0),
			array(0,0,0,0,0,0,0,0,0,0),array(0,0,0,0,0,0,0,0,0,0));
	var $isValid=true;

	function insert($String){
		$parts=explode(",", $String);
		 
		switch ($parts[0]){
			case 'Aircraft carrier': $this->Aircraft($parts[1],$parts[2],$parts[3]); break;
			case 'Battleship': $this->Battleship($parts[1],$parts[2],$parts[3]); break;
			case 'Frigate': $this->FriSub($parts[1],$parts[2],$parts[3]); break;
			case 'Submarine': $this->FriSub($parts[1],$parts[2],$parts[3]); break;
			case 'Minesweeper': $this->MineSweep($parts[1],$parts[2],$parts[3]); break;
			default:  echo 'Error'; $this->isValid=false; break;
		}
		 
	}

	function Aircraft($x,$y,$dir){
		if($dir){
			$this->Horizontal($x,$y,5);
		}else{
			$this->Vertical($x,$y,5);
		}
	}

	function Battleship($x,$y,$dir){
		if($dir){
			$this->Horizontal($x,$y,4);
		}else{
			$this->Vertical($x,$y,4);
		}
	}

	function FriSub($x,$y,$dir){
		if($dir){
			$this->Horizontal($x,$y,3);
		}else{
			$this->Vertical($x,$y,3);
		}
	}

	function MineSweep($x,$y,$dir){
		if($dir){
			$this->Horizontal($x,$y,2);
		}else{
			$this->Vertical($x,$y,2);
		}
	}

	function Horizontal($x,$y,$s){
		for($i=$x-1;$i<$s;$i++){
			if($this->board[$i][$y-1]===0){
				$this->board[$i][$y-1]=1;
			}else{
				echo "Error";
				$this->isValid=false;
			}
		}
	}

	function Vertical($x,$y,$s){
		for($i=$y-1;$i<$s;$i++){
			if($this->board[$x-1][$i]===0){
				$this->board[$x-1][$i]=1;
			}else{
				echo "Error";
				$this->isValid=false;
				 
			}
		}
	}
}