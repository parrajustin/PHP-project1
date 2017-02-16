<html>
<head>
</head>
<body>
  <?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
?>
</body>
</html>

<?php
var_dump(require("../common/flat.php"));
require_once('../common/flat.php');
$db = new flat('../data');
$db->doc('setting')->insert(array(
  "size" => strval(10),
  "strategies" => join(',', array("Smart", "Random", "Sweep", "test")),
  "ships" => join(',', array("battleship", "frigate", "submarine", "minesweeper", "aircraft carrier")), ));

$data = $db->doc('setting')->find("10", "size");
echo join(',', array_keys($db->doc('setting')->find("10", "size")));

$size = $data['size'];
$strategies = explode(',', $data['strategies']);

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