
<?php
// SETUP DATABASE
require_once('../common/flat.php');
require_once('../common/common.php');
$setup = new check();
$setup->run();
$db = new flat('../data');
// ===== END SETUP

$db->doc('setting');

//TODO: $data = $db->doc('setting')->findall()[0];
$data = $db->find("10", "size"); // set data to the query looking for the most recent settings insert
$data['strategies'] = json_decode($data['strategies']); // from data turn strategies string "a,b,c,d" = array("a", "b", "c", "d")
$data['ships'] = json_decode($data['ships']);
unset($data['id']);

echo json_encode($data);
?>
