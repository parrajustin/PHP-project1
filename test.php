<?php
// torture.php - tortue a battleship web service by performing
// various tests, esp. error handling.

// set to the base address (URL) of a battleship web service
$home = "http://localhost/";
//$home = "http://cs3360.cs.utep.edu/<ur-account>/";
//$home = "http://www.cs.utep.edu/cheon/cs3360/project/battleship/";

$strategies = ["Random"]; // strategies supported by the web service under test

runTests();

/** Test info. */
function testInfo() {
    global $home;
    global $strategies;
    $TAG = "I1";
    $string = file_get_contents($home . "info/index.php");
    if ($string) {
        $info = json_decode($string);
        if ($info != null) {
            $size = $info->{'size'};
            assertTrue(isSet($size) && $size == 10, "$TAG-1");
            $strategies = $info->{'strategies'};
            assertTrue(isSet($strategies) && is_array($strategies)
                && sizeof($strategies) >= 3, "$TAG-2");
            checkShips($info->{'ships'});
            return;
        }
    }
    fail("$TAG-3");
}

function checkShips($ships) {
    $TAG = "I3";
    $expected = ["aircraft carrier" => 5, "battleship" => 4,
        "frigate" => 3,	"submarine" => 3, "minesweeper" => 2];
    assertTrue(isSet($ships) && is_array($ships) && sizeof($ships) == 5,
        "$TAG-4");
    foreach ($ships as $ship) {
        $received[strtolower($ship->{'name'})] = $ship->{'size'};
    }
    assertTrue($expected == $received, "$TAG-5");
}

/** Test: all strategies. */
function testNew1() {
    global $strategies;
    foreach ($strategies as $s) {
        $response = visitNew($s);
        checkNewResponse($response, true, "N1");
    }
}

/** Test: strategy not specified. */
function testNew2() {
    $response = visitNew();
    checkNewResponse($response, false, "N2");
}

/** Test: unknown strategy. */
function testNew3() {
    $response = visitNew('Strategy' . uniqid());
    checkNewResponse($response, false, "N3");
}

/** Test: ship not well-formed. */
function testNew4() {
    global $strategies;
    $ships = "Aircraft+carrier,1,6,7,false;Battleship,7,5,true;"
            . "Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false";
    $response = visitNew($strategies[0], $ships);
    //var_dump($response);
    checkNewResponse($response, false, "N4");
}

/** Test: unknwon ship. */
function testNew5() {
    global $strategies;
    $ships = "Aircraft,1,6,false;Battleship,7,5,true;"
            . "Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false";
    $response = visitNew($strategies[0], $ships);
    //var_dump($response);
    checkNewResponse($response, false, "N5");
}

/** Test: invalid ship position, x. */
function testNew6() {
    global $strategies;
    $ships = "Aircraft+carrier,1,6,false;Battleship,7,5,true;"
            . "Frigate,2,1,false;Submarine,9,6,false;Minesweeper,12,9,false";
    $response = visitNew($strategies[0], $ships);
    //var_dump($response);
    checkNewResponse($response, false, "N6");
}

/** Test: invalid ship position, y. */
function testNew7() {
    global $strategies;
    $ships = "Aircraft+carrier,1,6,false;Battleship,7,5,true;"
            . "Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,0,false";
    $response = visitNew($strategies[0], $ships);
    //var_dump($response);
    checkNewResponse($response, false, "N7");
}

/** Test: invalid ship direction, UP. */
function testNew8() {
    global $strategies;
    $ships = "Aircraft+carrier,1,6,false;Battleship,7,5,UP;"
            . "Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false";
    $response = visitNew($strategies[0], $ships);
    //var_dump($response);
    checkNewResponse($response, false, "N8");
}

/** Test: incomplete ship placements (missing ship). */
function testNew9() {
    global $strategies;
    $ships = "Battleship,7,5,true;Frigate,2,1,false;"
            . "Submarine,9,6,false;Minesweeper,10,9,false";
    $response = visitNew($strategies[0], $ships);
    //var_dump($response);
    checkNewResponse($response, false, "N9");
}

/** Test: conflicting (overlapping) ship placements. */
function testNew10() {
    global $strategies;
    $ships = "Aircraft+carrier,1,6,false;Battleship,1,6,true;"
            . "Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false";
    $response = visitNew($strategies[0], $ships);
    //var_dump($response);
    checkNewResponse($response, false, "N10");
}

/** Test: no pid specified. */
function testPlay1() {
    $response = visitPlay();
    //var_dump($response);
    checkPlayResponse($response, false, "P1");
}

/** Test: no shot specified. */
function testPlay2() {
    $response = visitPlay(createGame());
    //var_dump($response);
    checkPlayResponse($response, false, "P2");
}

/** Test: unknown pid. */
function testPlay3() {
    $response = visitPlay('pid-' . uniqid(), "1,1");
    //var_dump($response);
    checkPlayResponse($response, false, "P3");
}

/** Test: shot not well-formed. */
function testPlay4() {
    $response = visitPlay(createGame(), "10");
    //var_dump($response);
    checkPlayResponse($response, false, "P4");
}

/** Test: shot not well-formed. */
function testPlay5() {
    $response = visitPlay(createGame(), "1,2,3");
    //var_dump($response);
    checkPlayResponse($response, false, "P5");
}

/** Test: invalid shot position, x. */
function testPlay6() {
    $response = visitPlay(createGame(), "0,5");
    //var_dump($response);
    checkPlayResponse($response, false, "P6");
}

/** Test: invalid shot position, y. */
function testPlay7() {
    $response = visitPlay(createGame(), "5,11");
    //var_dump($response);
    checkPlayResponse($response, false, "P7");
}

/** Test: already hit. */
function testPlay8() {
    $TAG = "P8";
    $pid = createGame();
    $response = visitPlay($pid, "5,5");
    //var_dump($response);
    checkPlayResponse($response, true, "$TAG-1");
    $response = visitPlay($pid, "5,5");
    //var_dump($response);
    checkPlayResponse($response, false, "$TAG-2");
}

/** Test: single partial game. */
function testPlay9() {
    $TAG = "P9";
    $pid = createGame();
    $response = visitPlay($pid, "1,1");
    checkPlayResponse($response, true, "$TAG-1");
    $response = visitPlay($pid, "1,10");
    checkPlayResponse($response, true, "$TAG-2");
    $response = visitPlay($pid, "5,5");
    checkPlayResponse($response, true, "$TAG-3");
    $response = visitPlay($pid, "10,1");
    checkPlayResponse($response, true, "$TAG-4");
    $response = visitPlay($pid, "10,10");
    checkPlayResponse($response, true, "$TAG-5");
}

/** Test: concurrent games. */
function testPlay10() {
    $TAG = "P10";
    $g1 = createGame();
    play($g1, "1,1", true, "$TAG-1");
    $g2 = createGame();
    play($g2, "1,1", true, "$TAG-2");
    play($g1, "4,5", true, "$TAG-3");
    play($g2, "9,6", true, "$TAG-4");

    play($g1, "4,5", false, "$TAG-5");
    play($g2, "9,6", false, "$TAG-6");
}

/** Test: sweep - hit all 100 places. */
function testPlay11() {
    $TAG = "P11";
    $pid = createGame();
    $ships1 = 0;
    $ships2 = 0;
    for ($x = 1; $x <= 10; $x ++) {
        for ($y = 1; $y <= 10; $y ++) {
            $response = visitPlay($pid, "$x,$y");
            $json = json_decode($response);
            assertTrue ($json->{'response'}, "$TAG-1");
            $shot = $json->{'ack_shot'}; // user
            if ($shot->{'isSunk'}) {
                $ships1 ++;
            }
            if ($shot->{'isWin'}) {
                assertTrue($ships1 == 5, "$TAG-2");
                return;
            }
            $shot = $json->{'shot'}; // computer
            if ($shot->{'isSunk'}) {
                $ships2 ++;
            }
            if ($shot->{'isWin'}) {
                assertTrue($ships2 == 5, "$TAG-3");
                return;
            }
        }
    }
    assertTrue($isOver, "$TAG-4");
}

function visitNew($strategy = null, $ships = null) {
    global $home;
    $query = '';
    if (!is_null($strategy)) {
        $query = '?strategy=' . $strategy;
    }
    if (!is_null($ships)) {
        $query = $query . (strlen($query) > 0 ? '&' : '?');
        $query = $query . 'ships=' . $ships;
    }
    return file_get_contents($home . "new/index.php" . $query);
}

function checkNewResponse($response, $expected, $msg) {
    if ($response) {
        $json = json_decode($response);
        if ($json != null) {
            $r = $json->{'response'};
            assertTrue(isSet($r) && $r == $expected, $msg);
            if ($expected) {
                assertTrue(isSet($json->{'pid'}), $msg);
            }
            return;
        }
    }
    fail($msg);
}

function createGame() {
    global $strategies;
    $ships = "Aircraft+carrier,1,6,false;Battleship,7,5,true;"
            . "Frigate,2,1,false;Submarine,9,6,false;Minesweeper,10,9,false";
    $response = visitNew($strategies[0], $ships);
    $json = json_decode($response);
    return $json->{'pid'};
}

function play($pid = null, $shot = null, $ok, $tag) {
    $response = visitPlay($pid, $shot);
    checkPlayResponse($response, $ok, $tag);
}

function visitPlay($pid = null, $shot = null) {
    global $home;
    $query = '';
    if (!is_null($pid)) {
        $query = '?pid=' . $pid;
    }
    if (!is_null($shot)) {
        $query = $query . (strlen($query) > 0 ? '&' : '?');
        $query = $query . 'shot=' . $shot;
    }
    return file_get_contents($home . "play/index.php" . $query);
}

function checkPlayResponse($response, $expected, $msg) {
    if ($response) {
        $json = json_decode($response);
        if ($json != null) {
            $r = $json->{'response'};
            assertTrue(isSet($r) && $r == $expected, $msg);
            if ($expected) {
                assertTrue(isSet($json->{'ack_shot'}), $msg);
            }
            return;
        }
    }
    fail($msg);
}

//---------------------------------------------------------------------
// Simple testing framework
//---------------------------------------------------------------------

/** Run all user-defined functions named 'test'. */
function runTests() {
    $count = 0;
    $prefix = "test";
    $names = get_defined_functions () ['user'];
    foreach ($names as $name)  {
        if (substr($name, 0, strlen($prefix)) === $prefix) {
            $count ++;
            echo ".";
            call_user_func($name);
        }
    }
    summary($count, fail('', false));
}

function assertTrue($expr, $msg) {
    if (!$expr) {
        fail($msg);
    }
}

function fail($msg, $report = true) {
    static $count = 0;
    if ($report) {
        $count++;
        echo "F($msg)";
    }
    return $count;
}

function summary($total, $failed) {
    echo "\n";
    echo "Failed/Total: $failed/$total\n";
}

?>
