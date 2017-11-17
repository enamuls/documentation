<?php

$API_KEY = "AIzaSyA7KWSTA-8SbU3HdJYfkyEzSNBeRoks8YA";

$origin_address = "20KelvinsideDr,TemplestoweVIC3106";

$destination_address = "AustinHospital,145StudleyRd,HeidelbergVIC3084";

$xml = file_get_contents("https://maps.googleapis.com/maps/api/directions/json?origin=$origin_address&destination=$destination_address&alternatives=true&departure_time=now&key=$API_KEY");

echo $xml;

?>