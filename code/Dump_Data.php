
<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();

$flag = FALSE;

$addresses = array("address1", "address2", "address3", "address4", "address5", "address6",
    "address7", "address8", "address9", "address10", "address11", "address12",
    "address13", "address14", "address15", "address16");

// Upload File to the server
$file_name = $db->uploadFile();

if ($file_name === FALSE) {
    // File does not exist
} else {
// Declare all the variables to store data
    $table_name = null;
    $day = null;
    $date = null;
    $time = null;
    $duration1 = null;
    $distance1 = null;
    $duration2 = null;
    $distance2 = null;
    $duration3 = null;
    $distance3 = null;
    $duration4 = null;
    $distance4 = null;
    $duration5 = null;
    $distance5 = null;

// Record variables
    $record_new = 0;
    $record_exists = 0;
    $record_empty = 0;

// Open text file to read from in "r" readonly mode
    $myfile = fopen($file_name, "r") or die("Unable to open file!<br>");

// Output one line until end-of-file
    while (!feof($myfile)) {

        $string = fgets($myfile);

        $token = strtok($string, ",");

        $tokenCount = 0;

        // Check if the file is in right format
        for ($x = 0; $x < 16; $x++) {
            if (strcmp($token, $addresses[$x]) == 0) {
                $flag = TRUE;
            } else {
                
            }
        }

        if ($flag === FALSE) {
            echo "<br>Data in the file is incorrectly formated";
            break;
        }

        while ($token !== false) {
            //echo "$token<br>";

            if ($tokenCount == 0) {
                $table_name = $token;
            }

            if ($tokenCount == 1) {
                $day = $token;
            }

            if ($tokenCount == 2) {
                $date = $token;
            }

            if ($tokenCount == 3) {
                $time = $token;
            }

            if ($tokenCount == 4) {
                $duration1 = $token;
            }

            if ($tokenCount == 5) {
                $distance1 = $token;
            }

            if ($tokenCount == 6) {
                $duration2 = $token;
            }

            if ($tokenCount == 7) {
                $distance2 = $token;
            }

            if ($tokenCount == 8) {
                $duration3 = $token;
            }

            if ($tokenCount == 9) {
                $distance3 = $token;
            }

            if ($tokenCount == 10) {
                $duration4 = $token;
            }

            if ($tokenCount == 11) {
                $distance4 = $token;
            }

            if ($tokenCount == 12) {
                $duration5 = $token;
            }

            if ($tokenCount == 13) {
                $distance5 = $token;
            }


            $token = strtok(",");

            $tokenCount++;
        }

        $result = $db->isDataUnique($table_name, $date, $time);

        if ($result->num_rows > 0) {
            $record_exists++;
        } else {

            if ($distance1 != null && $duration1 != null) {

                if ($db->addNewRecord($table_name, $day, $date, $time, $duration1, $distance1, $duration2, $distance2, $duration3, $distance3, $duration4, $distance4, $duration5, $distance5) === TRUE) {
                    $record_new++;
                } else {
                    
                }
            } else {
                $record_empty++;
            }
        }

        // Reset all the variable to reuse
        $day = null;
        $date = null;
        $time = null;
        $distance1 = null;
        $duration1 = null;
        $distance2 = null;
        $duration2 = null;
        $distance3 = null;
        $duration3 = null;
        $distance4 = null;
        $duration4 = null;
        $distance5 = null;
        $duration5 = null;
    }

    if ($flag === TRUE) {
        $db->addFileUploaded(date("ymd"), date("h:i:sa"), $record_new, $record_exists, $record_empty);
    }

    echo "<br><br>Unique records added : " . $record_new . "<br>";
    echo "<br>Records not unique : " . $record_exists . "<br>";
    echo "<br>Records with no data : " . $record_empty . "<br>";

    // Close file connection
    fclose($myfile);

    // Delete the file from the server
    //unlink($file_name);
}

//header('Refresh: 5; url=http://operation3inc.com/wp-admin/CSE5ITP/UploadFileSystem.html');
//echo "<br>" . '<b>You will be redirected in 5 seconds</b>' . "<br>";
?>

<!DOCTYPE html>
<html>
    <head>

        <meta charset="UTF-8">

        <title>File Upload</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

        <style type="text/css">

            body{ font: 14px sans-serif; }

            .wrapper{ width: 350px; padding: 2px; }

        </style>

    </head>

    <body>

        <div class="wrapper">
            <br><br><br><br><br>
            <a style="height:35px;width:200px" href="UploadFileSystem.html" class="btn btn-danger"><b>Back</b></a>
            <br>
        </div>    

    </body>
</html>