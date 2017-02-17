<?php

/*  So we can handle exceptions here */
//class Exception extends \Exception {}

class flat {
  private $dir = ""; // directory to database
  private $table = ""; // current table name
  private $meta_data = array(); // contains the metadata for all operated tables
  private $returnable = array();

  /**
   *  Constructor creates the database
   */
  public function __construct($path, $db_name = 'default') {
    $this->dir = $path . (substr(strval($path), -1) === '/'? '' : '/') . $db_name . (substr(strval($db_name), -1) === '/'? '' : '/'); // substr checks if the last char is already a /
    
    //Can the directory even exist?
    if( !is_dir($this->dir) ) {
      if( !mkdir($this->dir, 0744, true) )
        throw new Exception('Failed to make directory, permission failed');
      else
        file_put_contents($this->dir . 'index.php', "<?php"); // add an index.php so that the database can't be indexed
    }
  }

  public function doc($table_name) {
    $this->table = $table_name;
    return $this;
  }

  public function count() {
    $meta = $this->meta();

    if( is_null($meta) )
      return 0;
    else
      return $meta['count'];
  }
  
  public function insert($input) {
    
    if( !is_array($input) )
      throw new Exception('Can only input key-value pairs!');
    if( array_key_exists('id', $input) ) // you can't have an object with the key id
      throw new Exception('The key id is a reserved keyword');
    
    $table = $this->table; // quick way to access the table
    $id = 0; // the id of the insert
    $meta = null; // data of the table, holds count and id and indexes
    $update_keys = 0;
    
    if( !is_dir($this->dir . $table) ) { // does this table even exist?
      if( !mkdir($this->dir . $table, 0777) )
        throw new Exception('Failed to make directory, permission failed');
      else
        file_put_contents($this->dir . $table . '/index.php', '<?php');

      $id = 0; // flat table directory id starts at 0
      $meta = array(
        'last_id' => 0,
        'count' => 0,
        'keys' => array(),
        'summary' => array(),
      );

      $update_keys = 1;
    } else { // it does exist
      $meta = $this->meta(); // retrieve the meta data from meta.php
      $id = $meta['last_id'];
    }

    $input['id'] = $id; // we need to have an identifier
    ksort($input);
    
    if( $update_keys === 0 && !(array_keys($input) === $meta['keys']) ) { // if the input being inserted doesn't have the right keys
      $temp_input_keys = strval(join(",", array_keys($input)));
      $temp_meta_keys = strval(join(",", $meta['keys']));
      throw new Exception("Input array must have the same keys as the rest of the table, input: $temp_input_keys, keys: $temp_meta_keys");
    } else if( $update_keys === 1 ) { // this is the first time inserting into the table so all subsequent keys will be based off of these obj's keys
      $meta['keys'] = array_keys($input);
      sort($meta['keys']);
    }

    //add to summary
    foreach ( $meta['keys'] as $value) {
      if( !array_key_exists($value, $meta['summary']) )
        $meta['summary'][$value] = array();

      
      $meta['summary'][$value][$input[$value]] = $input['id'];
    }

    $this->write($this->dir . $table . '/' . strval($id) . '.php', $input); // create new file

    $meta['last_id'] = $meta['last_id']  + 1;
    $meta['count'] = $meta['count'] + 1;
    $this->meta_data[$table] = $meta;
    $this->write($this->dir . $table . '/meta.php', $meta);

    $returnable['bool'] = 1;
    $returnable['selector'] = strval($id); // make it so that this is the selcted object
    return $this; // continue chainability
  }

  public function find($value, $field = 'id') {
    $meta = $this->meta();

    if( $meta['count'] === 0 || !in_array($field, $meta['keys']) || !array_key_exists(strval($value), $meta['summary'][$field]) )
      return array();

    $item_id = $meta['summary'][$field][strval($value)];
    return $this->get($item_id);
  }

  private function get($id) {
    $table = $this->table;

    if( file_exists($this->dir . $table . '/' . $id . '.php') ) {
      return $this->read($this->dir . $table . '/' . $id . '.php');
    }
    return null;
  }

  private function read($path) {
    $contents = file_get_contents($path);
    return unserialize(substr($contents, 16));
  }
  
  private function write($path, $obj) {
    file_put_contents($path, '<?php exit(); ?>' . serialize($obj), LOCK_EX);
  }

  /**
   * Returns the meta data for a table
   */
  public function meta() {
    $table = $this->table;

    if( !array_key_exists($table, $this->meta_data) ) {
      $path = $this->dir . $table . '/meta.php';
      if( !file_exists($path) )
        return NULL;

      $this->meta_data[$table] = $this->read($path, false);
    }

    return $this->meta_data[$table];
  }
}
?>




