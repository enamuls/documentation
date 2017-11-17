<?php

$API_KEY = "AIzaSyBo33WFp6-fykH3IxVMc77jQOclonSRLPU";

$origin_address = "5MonakPl,YallambieVIC3085";

$destination_address = "AustinHospital,145StudleyRd,HeidelbergVIC3084";

$xml = file_get_contents("https://maps.googleapis.com/maps/api/directions/json?origin=$origin_address&destination=$destination_address&alternatives=true&departure_time=now&key=$API_KEY");

echo $xml;

?>