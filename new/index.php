<?php

require_once('../common/Board.php');

$strat= $_GET['strategy'];
$ships= $_GET['ships'];

$ships=explode(';', $ships);


$board=new Borad();

for($i=0;$i<count($ships);$i++){
	$board->insert($ships[$i]);
}

if ($board->isValid){
	$id=uniqid();
	echo '{"response": true, "pid": '.$id.'}';
}



?>