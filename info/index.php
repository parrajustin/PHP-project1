<?php

$size = 10;
$strategies = array("Smart", "Random", "Sweep");

// print all the way up till the strategies array
printf( "{\"size\": %s, \"strategies\": [", strval($size));

for($i = 0; $i < sizeof($strategies); $i++) {
       if( $i != 0) {
       	   printf(" ,");
       }
       printf("\"%s\"", strval($strategies[$i]));
}

printf("], \"ships\": [output ships here]}");

?>