# Common Folder
This file explains the files contined in this folder

## index.php
- is an empty php tag so that this folder won't be indexed

## flat.php
- This file contains Justin Parra's implementation of a flat file DATABASE
- Only contains the necessary method needed to make this battleship api work

The flat file class "flat" contains the following methods
'''php
  public function doc($table_name);
  public function count();
  public function insert($input);
  public function find($value, $field = 'id');
  private function get($id);
  private function read($path);
  private function write($path, $obj);
  public function meta();
  public function update($id, $field, $array);
'''

## common.php
- Handles the underlying work needed for the battleship api to work
- The purpose of this file is to make it easier to access the stored files for the game, such as ship info, board size, ship locations ... etc

The class contained within is called "game" and it contains the following methods
'''php
  public function get_board_size();
  public function get_avaliable_strategies();
  public function get_avaliable_ship_array();
  public function get_ship_size($name = "a");
  public function get_ship_names();
  public function create_pid();
  public function get_pid();
  public function insert_game($insertee);
  public function get_game($pid = "a");
  public function set_pid($pid);
  public function get_strategy();
  public function get_player_ships();
  public function get_computer_ships();
  public function get_player_shots();
  public function get_computer_shots();
  public function shot_exists($col, $row, $is_player);
  public function game_over();
  public function update_game($pid, $array);
'''
