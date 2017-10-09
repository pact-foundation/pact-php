<?php
/*
    Mock Provider as an example

*/

if (isset($_GET["amount"])) {
    $objects = generate(intval($_GET["amount"]));
} elseif (isset($_GET["file"])) {
    $fileName = filter_var($_GET["file"], FILTER_SANITIZE_STRING);
    $currentDir = dirname(__FILE__);
    $relativeDir = $currentDir . DIRECTORY_SEPARATOR . $fileName;
    error_log("File get: " . $relativeDir);

    $objects = \json_decode(file_get_contents($relativeDir));
} elseif (!empty($_POST)) {
    error_log('received post');

    $body = '{ "type": "some new type" }';
    $body = \json_encode(\json_decode($body));
    $objects = \json_decode($body);
} else {
    $objects = generate();
}

header('Content-Type: application/json');
echo json_encode($objects);

function generate($objCount = 3)
{
    $objects = array();
    
    for ($i=0;$i<$objCount;$i++) {
        $obj = new \stdClass();
        $obj->id = 100 + $i;
        $obj->name = sprintf("Type %d", $obj->id);
        
        $objects[] = $obj;
    }
    
    $ret = new \stdClass();
    $ret->types = $objects;
    return $ret;
}
