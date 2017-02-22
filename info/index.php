
<?php
// SETUP DATABASE
require_once('../common/flat.php');
require_once('../common/common.php');
$game = new game();
// ===== END SETUP

echo json_encode(array(
  "size" => $game->get_board_size(),
  "strategies" => $game->get_avaliable_strategies(),
  "ships" => $game->get_avaliable_ship_array(),
));
?>
