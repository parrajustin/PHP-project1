<?php
/**
 *   @author Justin R. Parra <jrparra2@miners.utep.edu>
 *   @purpose To handle to low level manipulation of the data that is stored in a flat file format under the data folder
 */

/** A flat file storage implementation */
class flat {
  /**
   *   The directory to the database
   *   @var string
   */
  private $dir = ""; // directory to database
  /**
   *   The current table we are working in
   *   @var string
   */
  private $table = ""; // current table name
  /**
   *   An array the contains all the meta data that we have worked on
   *   @var array
   */
  private $meta_data = array(); // contains the metadata for all operated tables

  /**
   *   Creates the flat file database
   *   @method __construct
   *   @param  string      $path    the path to the database folder
   *   @param  string      $db_name default database name
   */
  public function __construct($path, $db_name = 'default') {
    $this->dir = $path . (substr(strval($path), -1) === '/'? '' : '/') . $db_name . (substr(strval($db_name), -1) === '/'? '' : '/'); // substr checks if the last char is already a /

    //Can the directory even exist?
    if( !is_dir($this->dir) ) {
      if( !mkdir($this->dir, 0744, true) )
        throw new Exception('Failed to make directory');
      else
        file_put_contents($this->dir . 'index.php', "<?php"); // add an index.php so that the database can't be indexed
    }
  }

  /**
   *   Chooses the table to work in, Once set all instances of the object will be in that table
   *   @method doc
   *   @param  string $table_name the name of the table to work in
   *   @return flat             returns this object so that this method can be chained, or NULL if table_name isn't a string
   */
  public function doc($table_name) {
    if( !is_string($table_name) )
      return NULL;

    $this->table = $table_name;
    return $this; // makes this chainable
  }

  /**
   *   Returns the number of elements in the table
   *   @method count
   *   @return int the count of elements
   */
  public function count() {
    $meta = $this->meta();

    if( is_null($meta) )
      return 0;
    else
      return $meta['count'];
  }

  /**
   *   inserts the key value array into the database
   *   @method insert
   *   @param  array $input array to insert into the table
   *   @return flat        returns this object so that this method may be chained
   */
  public function insert($input) {

    if( $this->table === "" )
      throw new Exception("Table isn't set");
    if( !is_array($input) )
      throw new Exception('Can only input key-value pairs!');
    if( array_key_exists('id', $input) ) // you can't have an object with the key id
      throw new Exception('The key id is a reserved keyword');

    $table = $this->table; // quick way to access the table
    $id = 0; // the id of the insert
    $meta = null; // data of the table, holds count and id and indexes
    $update_keys = 0; // do we have to update the keys array, only need to be done on new tables

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

    return $this; // continue chainability
  }

  /**
   *   Finds a value in the key field
   *   @method find
   *   @param  string $value the value to find
   *   @param  string $field the key field to search for the value
   *   @return array        the item
   */
  public function find($value, $field = 'id') {
    $meta = $this->meta();

    if( $meta['count'] === 0 || !in_array($field, $meta['keys']) || !array_key_exists(strval($value), $meta['summary'][$field]) )
      return array();

    $item_id = $meta['summary'][$field][strval($value)];
    return $this->get($item_id);
  }

  /**
   *   gets a certain element by its id
   *   @method get
   *   @param  int $id the id to look for
   *   @return array     the item your looking for
   */
  private function get($id) {
    $table = $this->table;

    if( file_exists($this->dir . $table . '/' . $id . '.php') ) {
      return $this->read($this->dir . $table . '/' . $id . '.php');
    }
    return null;
  }

  /**
   *   Reads data from path
   *   @method read
   *   @param  string $path the path to the file to read
  *   @return object        returns the data that was read
   */
  private function read($path) {
    $contents = file_get_contents($path);
    return unserialize(substr($contents, 16));
  }

  /**
   *   Writes the to a file
   *   @method write
   *   @param  string $path the string path of where to write to
   *   @param  object $obj  what to write, it will be serialized
   */
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

  /**
   *   Updates an entry in the given database
   *   @method update
   *   @param  string $id    the id of the field
   *   @param  string $field the field for id
   *   @param  array $array key value pair to update array
   */
  public function update($id, $field, $array) {
    $holder = $this->find($id, $field);
    foreach ($array as $key => $value) {
      if( array_key_exists($key, $holder) )
        $holder[$key] = $value;
    }

    $this->write($this->dir . $this->table . '/' . strval($holder['id']) . '.php', $holder); // create new file
  }
}
?>
