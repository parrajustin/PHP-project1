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
require_once('../common/default.php');
$setup = new check();
$setup->run();

$db = new flat('../data');
$db->doc('setting');

//TODO: $data = $db->doc('setting')->findall();
$data = $db->doc('setting')->find("10", "size"); // set data to the query looking for the most recent settings insert
$data['strategies'] = json_decode($data['strategies']); // from data turn strategies string "a,b,c,d" = array("a", "b", "c", "d")
$data['ships'] = json_decode($data['ships']);
unset($data['id']);

echo json_encode($data);
?>