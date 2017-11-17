<?php

require_once 'include/DB_Functions.php';

$db = new DB_Functions();

$response = array("error" => FALSE);

/*if (isset($_POST['table_name']) && isset($_POST['distance']) && isset($_POST['duration'])){    
    $table_name = $_POST['table_name'];
    $distance = $_POST['distance'];
    $duration = $_POST['duration'];*/
if (isset($_POST['table_name'])){    
    $table_name = $_POST['table_name'];
    
    //$result = $db->getRecordForJava($table_name, $distance, $duration);
    $result = $db->getAllRecord($table_name);
}



//$result = $db->getRecord($table_name, $distance, $duration);



