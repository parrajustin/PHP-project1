
<?php
/**
 *   @author Justin R. Parra <jrparra2@miners.utep.edu>
 *   @author Sebastian A. Urtaza <Sayalaurtaza@miners.utep.edu>
 *   @author Luis Romero <Lgromero2@miners.utep.edu>
 *   @purpose this is a simple implementation of the info api
 */

// SETUP DATABASE
require_once('../common/common.php');
$game = new game();
// ===== END SETUP

echo json_encode(array(
  "size" => $game->get_board_size(),
  "strategies" => $game->get_avaliable_strategies(),
  "ships" => $game->get_avaliable_ship_array(),
));
?>
