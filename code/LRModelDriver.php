<?php
require_once 'include/DB_Functions.php';
require_once 'Linear_Regression.php';

// POST variables
$table_name = $_POST["address"];
$hour_selected = $_POST["hour"];
$minute_selected = $_POST["minute"];
$ampm_selected = $_POST["ampm"];
$current_time = date("h:i:sa");
$current_day = date('l');
$current_date = date("m/d/y");
$siren_flag = FALSE;

// siren on checkbox
if(isset($_POST['siren']) && $_POST['siren'] == 'on') {
    $siren_flag = TRUE;
    echo "Siren is on";
} else {
    echo "Siren is off";
}

// API key for Google Maps API
$API_KEY = "AIzaSyD7Y8ow8NgblNiJg4G6Db7wjbhrIFRj5rU";

// Austin Health Hospital coordinates
$destination_address = "-37.756412,145.060279";

// define DB_Functions for SQL interactions
$db = new DB_Functions();

// define Linear_Regression for analysis
$lr = new Linear_Regression();

// variables, arrays for data preparation to run linear regression
$x = array();
$y = array();
$d = array();
$eta = array();
$error = array();
$source = "";
$days = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");

//set origin address according to the address passed
if (strcasecmp($table_name, "address1") === 0) {
    $origin_address = "-37.756503,145.153845";
    $source = "16-18 Smiths Rd, Templestowe VIC 3106";
}
if (strcasecmp($table_name, "address2") === 0) {
    $origin_address = "-37.733074,145.144244";
    $source = "21 Antoinette Blvd, Eltham VIC 3095";
}
if (strcasecmp($table_name, "address3") === 0) {
    $origin_address = "-37.689111,145.073154";
    $source = "50 Hughes Circuit, Bundoora VIC 3083";
}
if (strcasecmp($table_name, "address4") === 0) {
    $origin_address = "-37.704949,145.116558";
    $source = "170 Mountain View Rd, Briar Hill VIC 3088";
}
if (strcasecmp($table_name, "address5") === 0) {
    $origin_address = "-37.723922,145.103211";
    $source = "5 Monak Pl, Yallambie VIC 3085";
}
if (strcasecmp($table_name, "address6") === 0) {
    $origin_address = "-37.738184,145.116964";
    $source = "1 Amberley Way, Lower Plenty VIC 3093";
}
if (strcasecmp($table_name, "address7") === 0) {
    $origin_address = "-37.706412,145.061155";
    $source = "79 Moreton Cres, Bundoora VIC 3083";
}
if (strcasecmp($table_name, "address8") === 0) {
    $origin_address = "-37.705663,145.027153";
    $source = "38 Hickford St, Reservoir VIC 3073";
}
if (strcasecmp($table_name, "address9") === 0) {
    $origin_address = "-37.759357,145.009380";
    $source = "63 Wales St, Thornbury VIC 3071";
}
if (strcasecmp($table_name, "address10") === 0) {
    $origin_address = "-37.743014,145.012717";
    $source = "10 Sinnott St, Preston VIC 3072";
}
if (strcasecmp($table_name, "address11") === 0) {
    $origin_address = "-37.711220,145.065904";
    $source = "5 Crestwood Ave, Macleod VIC 3085";
}
if (strcasecmp($table_name, "address12") === 0) {
    $origin_address = "-37.694523,145.038432";
    $source = "175 Settlement Rd, Thomastown VIC 3074";
}
if (strcasecmp($table_name, "address13") === 0) {
    $origin_address = "-37.812233,145.058537";
    $source = "13 Mountain Grove, Kew VIC 3101";
}
if (strcasecmp($table_name, "address14") === 0) {
    $origin_address = "-37.815131,145.031129";
    $source = "17 Hawthorn Grove, Hawthorn VIC 3122";
}
if (strcasecmp($table_name, "address15") === 0) {
    $origin_address = "-37.773917,145.000321";
    $source = "15 Bastings St, Northcote VIC 3070";
}
if (strcasecmp($table_name, "address16") === 0) {
    $origin_address = "-37.792810,144.996529";
    $source = "66 Roseneath St, Clifton Hill VIC 3068";
}

// if no time selected by user set current time on the user machine
if (strcmp($hour_selected, "HH") === 0 || strcmp($minute_selected, "MM") === 0) {
    $hour_selected = date("h");
    $minute_selected = date("i");

    // check am or pm
    if (strcasecmp(date("a"), "pm") === 0) {
        $ampm_selected = "PM";
    } else {
        $ampm_selected = "AM";
    }
}

// convert user selected hour from 12 hour format to 24 hour format
if (strcasecmp($ampm_selected, "PM") === 0) {
    $temp = intval($hour_selected);
    $temp = $temp + 12;

    if ($temp >= 24) {
        $temp = 0;
        $hour_selected = "00";
    } else {
        $hour_selected = $temp;
    }
}

echo "ETA Generated at : " . $hour_selected . ":" . $minute_selected . "<br>";

// get all the records from database accoring to the address
$jsonResponse = json_decode($db->getRecord($table_name), true);

// itterate through the alternative routes
for ($direction_count = 1; $direction_count < 4; $direction_count++) {
    $flag = FALSE;
    $x = array();
    $y = array();
    $d = array();

    $duration = "duration" . $direction_count;

    // itterate through the json response
    for ($c = 0; $c < count($jsonResponse); $c++) {

        $time = $jsonResponse[$c]['time'];
        $day = $jsonResponse[$c]['day'];

        // if the day from the record is the current day
        if (strcasecmp($day, substr($current_day, 0, 3)) === 0 && $flag === FALSE) {

            $min = (intval(substr($time, 3, 2)) * 100) / 60;
            $hour = (intval(substr($time, 0, 2)) * 100);
            $value = intval($hour + $min);

            // comparison time to get data
            $compare_time = ((intval($hour_selected) * 100) + ((intval($minute_selected) * 100) / 60));

            if ($value >= ($compare_time - 30) && $value <= ($compare_time + 30)) {

                // if data set is at least more than 5
                if (intval($jsonResponse[$c][$duration]) > 5) {
                    array_push($d, $jsonResponse[$c]['day']);
                    array_push($x, $value);
                    array_push($y, $jsonResponse[$c][$duration]);
                }
            }
        }
    }

    if ($flag === FALSE) {

// convert user defined time
        $time = $hour_selected . ":" . $minute_selected;
        $min = (doubleval(substr($time, 3, 2)) * 100.00) / 60.00;
        $hour = (doubleval(substr($time, 0, 2)) * 100.00) + $min;

        if (count($x) > 5 && count($x) === count($y)) {
            echo "<br>Route " . $direction_count . " ";
            $lr->assignVariables($x, $y, $hour, $siren_flag);
            array_push($eta, $lr->getEta());
            array_push($error, $lr->getError());
        }
    } else {
        break;
    }
}

for ($c = 1; $c <= count($eta); $c++) {
    if ($c === 1) {
        $eta1 = $eta[$c-1];
        $error1 = $error[$c-1];
    }
    if ($c === 2) {
        $eta2 = $eta[$c-1];
        $error2 = $error[$c-1];
    }
    if ($c === 3) {
        $eta3 = $eta[$c-1];
        $error3 = $error[$c-1];
    }
}

// add eta generated to the record
$db->addEtaGenerated(date("ymd"), date("h:i:sa"), $source, $eta1, $error1, $eta2, $error2, $eta3, $error3);

//header('Refresh: 10; url=http://operation3inc.com/wp-admin/CSE5ITP/UploadFileSystem.html');
//echo "<br>" . '<b>You will be redirected in 10 seconds</b>' . "<br>";

?>
<!DOCTYPE html>
<html> 
    <head> 
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/> 

        <title>File Upload</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

        <style type="text/css">

            body{ font: 14px sans-serif; }

            .wrapper{ width: 350px; padding: 2px; }

        </style>
        <title>Generate ETA</title> 
        <script type="text/javascript"
        src="//maps.googleapis.com/maps/api/js?key=<?php echo $API_KEY ?>"></script>
    </head> 
    <body style="font-family: Arial; font-size: 18px;"> 
        <div class="wrapper">
            <br>
            <a style="height:35px;width:200px" href="UploadFileSystem.html" class="btn btn-danger"><b>Back</b></a>
            <br><br>
        </div> 
        <div style="width: 905px;">
            <div id="map" style="width: 600px; height: 600px; float: left;"></div> 
            <div id="panel" style="width: 300px; float: right;"></div> 
        </div>

        <script type="text/javascript">

            var directionsService = new google.maps.DirectionsService();
            var directionsDisplay = new google.maps.DirectionsRenderer();

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });


            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById('panel'));

            var request = {
                origin: '<?php echo $origin_address ?>',
                destination: '<?php echo $destination_address ?>',
                provideRouteAlternatives: true,
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };

            directionsService.route(request, function (response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                }
            });
        </script> 
        
    </body> 
</html>

