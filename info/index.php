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
require_once('../common/flat.php');
$db = new flat('../data');
$db->doc('setting')->insert(array(
  "size" => strval(10),
  "strategies" => join(',', array("Smart", "Random", "Sweep", "test")),
  "ships" => json_encode(array(
    array("name" => "Aircraft carrier", "size" => 5),
    array("name" => "Battleship", "size" => 4),
    array("name" => "Frigate", "size" => 3),
    array("name" => "Submarine", "size" => 3),
    array("name" => "Minesweeper", "size" => 2),
  )),
));


//TODO: $data = $db->doc('setting')->findall();
$data = $db->doc('setting')->find("10", "size"); // set data to the query looking for the most recent settings insert
// echo join(',', array_keys($db->doc('setting')->find("10", "size")));

$data['strategies'] = explode(',', $data['strategies']); // from data turn strategies string "a,b,c,d" = array("a", "b", "c", "d")
$data['ships'] = json_decode($data['ships']);

?>