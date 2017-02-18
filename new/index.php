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

//TODO: $data = $db->doc('setting')->findall()[0];
$data = $db->find("10", "size");
$db->doc('games');

if (isset($_POST["info"]))
  echo $_POST["info"]; 
else
  echo join(',', $_POST);
?>