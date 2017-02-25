# new Folder
This file explains the files contained in this folder

## index.php
- does most of the checking for the input and validation of data for creating a new game

## ships.php
- This file contains a class to check ships placed on the game board
- random ships returns an array of ships so that they are placed correctly
- place function checks each defined ship to see if it was placed correctly

``` php
  public function reset();
  public function random_ships();
  public function place($ship_info);
```
