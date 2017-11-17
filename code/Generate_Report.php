<!DOCTYPE html>
<html>
    <head>

        <meta charset="UTF-8">

        <title>Generate Report</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

        <style type="text/css">

            body{ font: 14px sans-serif; }

            .wrapper{ width: 350px; padding: 2px }

        </style>

    </head>

    <body>

        <div class="wrapper">
            <br>
            <a style="height:35px;width:200px" href="UploadFileSystem.html" class="btn btn-danger"><b>Back</b></a>
            <br><br>
        </div>    

    </body>
</html>

<?php

require_once 'include/DB_Functions.php';
require_once 'Linear_Regression.php';

// passed variables
$dayFrom = $_POST["dayFrom"];
$monthFrom = $_POST["monthFrom"];
$yearFrom = $_POST["yearFrom"];
$dayTo = $_POST["dayTo"];
$monthTo = $_POST["monthTo"];
$yearTo = $_POST["yearTo"];

$db = new DB_Functions();

$dateFrom = $yearFrom . $monthFrom . $dayFrom;
$dateTo = $yearTo . $monthTo . $dayTo;


echo "<b>ETA Report</b><br><br>";
// generate ETA report
$jsonResponse1 = json_decode($db->getEtaGenerated($dateFrom, $dateTo), true);

echo "Total ETA Generated between (" . $dayFrom . "/" . $monthFrom . "/" . $yearFrom . "-" .
 $dayTo . "/" . $monthTo . "/" . $yearTo . ") : <b>" . count($jsonResponse1) . "</b><br><br>";

for ($c = 0; $c < count($jsonResponse1); $c++) {
    echo $jsonResponse1[$c]['date'] . "  " .
    $jsonResponse1[$c]['time'] . "  From: " .
    $jsonResponse1[$c]['source'] . "  To: " .
    $jsonResponse1[$c]['destination'] . "  ETA1: " .
    $jsonResponse1[$c]['eta1'] . " mins(+-" .
    $jsonResponse1[$c]['error1'] . " mins)  ETA2: " .
    $jsonResponse1[$c]['eta2'] . " mins(+-" .
    $jsonResponse1[$c]['error2'] . " mins)  ETA3: " .
    $jsonResponse1[$c]['eta3'] . " mins(+-" .
    $jsonResponse1[$c]['error3'] . " mins)<br>";
}
echo "<br><br>";

// generate File Upload report
$jsonResponse2 = json_decode($db->getFileUploaded($dateFrom, $dateTo), true);

echo "<b>File Upload History</b><br><br>";
for ($c = 0; $c < count($jsonResponse2); $c++) {
    echo $jsonResponse2[$c]['date'] . "  " .
    $jsonResponse2[$c]['time'] . "  " .
    $jsonResponse2[$c]['file_name'] . "<br>";
}
echo "<br><br>";

// error rate report after file upload

echo "<b>File Upload Impact on ETA error rate</b><br><br>";

$total_error = 0.0;
$count_error = 0;
$error_rate_before = 0.0;
$error_rate = array();

for ($c = 0; $c < count($jsonResponse2) - 1; $c++) {
    $dateFrom = $jsonResponse2[$c]['date'];
    $dateTo = $jsonResponse2[$c + 1]['date'];

    $jsonResponse3 = json_decode($db->getEtaGenerated($dateFrom, $dateTo), true);

    for ($i = 0; $i < count($jsonResponse3); $i++) {
        for ($errorCount = 1; $errorCount <= 3; $errorCount++) {
            //echo $jsonResponse3[$i]['error' . $errorCount] . "<br>";
            $total_error = $total_error + (double) ($jsonResponse3[$i]['error' . $errorCount]);
            //echo $total_error . "<br>";
            $count_error++;
        }
    }

    $error_rate_before = $total_error / $count_error;

    array_push($error_rate, round($error_rate_before, 2));
}

$dateFrom = $jsonResponse2[count($jsonResponse2) - 1]['date'];
$dateTo = date("ymd");

$jsonResponse3 = json_decode($db->getEtaGenerated($dateFrom, $dateTo), true);

for ($i = 0; $i < count($jsonResponse3); $i++) {
    for ($errorCount = 1; $errorCount <= 3; $errorCount++) {
        //echo $jsonResponse3[$i]['error' . $errorCount] . "<br>";
        $total_error = $total_error + (double) ($jsonResponse3[$i]['error' . $errorCount]);
        //echo $total_error . "<br>";
        $count_error++;
    }
}

$error_rate_before = $total_error / $count_error;

array_push($error_rate, round($error_rate_before, 2));

for ($c = 0; $c < count($error_rate); $c++) {
    echo "<b>File Uploaded : </b>" . $jsonResponse2[$c]['file_name'] . "<br>" .
            "New record added : " . $jsonResponse2[$c]['unique_record'] . "<br>" .
            "Duplicate record discarded : " . $jsonResponse2[$c]['duplicate_record'] . "<br>" .
            "Empty record with no data : " . $jsonResponse2[$c]['empty_record'] . "<br>" .
            "Accumulated error rate of ETA generated : " . $error_rate[$c] . "%<br><br>";
}

?>

