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
$db->doc('test')->insert(array(
    "a" => "be",
    "b" => "the",
    "c" => "best",))->insert(array(
      "a" => "the",
      "b" => "best",
      "c" => "be",));

echo join(',', $db->doc('test')->find("be", "a"));

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