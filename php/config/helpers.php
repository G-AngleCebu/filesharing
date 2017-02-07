<?php

// HELPER functions
function response($array, $exit = false){
    header("Content-Type:application/json");
	echo json_encode($array, JSON_NUMERIC_CHECK);

	if($exit) exit;
}

function issetAndNotEmpty($object){
    return isset($object) && !empty($object);
}