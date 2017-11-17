<?php

class DB_Functions {

    private $conn;
    private $file_name;

    // constructor
    function __construct() {
        require_once 'DB_Connect.php';

        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {
        
    }

    public function uploadFile() {

        $target_dir = "";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $this->file_name = $_FILES["fileToUpload"]["name"];
        $uploadOk = 1;
        $fileType = pathinfo($target_file, PATHINFO_EXTENSION);

        if (isset($_POST["submit"])) { $uploadOk = 1; }

        // Allow text file formats
        if ($fileType != "txt") {
            echo "Sorry, only text files are allowed.<br>";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.<br>"; return FALSE;

            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.<br>";
                return basename($_FILES["fileToUpload"]["name"]);
            } else {
                echo "Sorry, there was an error uploading your file.<br>"; return FALSE;
            }
        }
    }
    
    public function addFileUploaded ($date, $time, $unique, $duplicate, $empty) {
        $sql = "INSERT INTO file_uploaded (date, time, file_name, unique_record, duplicate_record, empty_record) 
		VALUES ('$date', '$time', '$this->file_name', $unique, $duplicate, $empty)";

        if ($this->conn->query($sql) === TRUE) {
            return TRUE;
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error . "<br>";
            return FALSE;
        }
    }
    
    public function addNewRecord($table_name, $day, $date, $time, $duration1, $distance1, $duration2, $distance2, $duration3, $distance3, $duration4, $distance4, $duration5, $distance5) {

        $sql = "INSERT INTO $table_name (day, date, time, duration1, distance1, duration2, distance2, duration3, distance3, duration4, distance4, duration5, distance5) 
		VALUES ('$day', '$date', '$time', '$duration1', '$distance1', '$duration2', '$distance2', '$duration3', '$distance3', '$duration4', '$distance4', '$duration5', '$distance5')";

        if ($this->conn->query($sql) === TRUE) {
            return TRUE;
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error . "<br>";
            return FALSE;
        }
    }
    
    public function addEtaGenerated($date, $time, $source, $eta1, $error1, $eta2, $error2, $eta3, $error3) {
        $destination = "Austin Hospital, 145 Studley Rd, Heidelberg VIC 3084";
                
        $sql = "INSERT INTO eta_generated (date, time, source, destination, eta1, error1, eta2, error2, eta3, error3) 
		VALUES ('$date', '$time', '$source', '$destination', '$eta1', '$error1', '$eta2', '$error2', '$eta3', '$error3')";

        if ($this->conn->query($sql) === TRUE) {
            return TRUE;
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error . "<br>";
            return FALSE;
        }
    }

    public function getFileName () {
        return $this->file_name;
    }
    
    public function getRecord($table_name) {

        $feed = array(); $query = "SELECT day, date, time, distance1, duration1, distance2, duration2, distance3, duration3, distance4, duration4, distance5, duration5 FROM $table_name";

        if ($result = $this->conn->query($query)) {

            while ($row = $result->fetch_assoc()) {

                $row_array['day'] = $row['day'];
                $row_array['date'] = $row['date'];
                $row_array['time'] = $row['time'];
                $row_array['distance1'] = $row['distance1'];
                $row_array['duration1'] = $row['duration1'];
                $row_array['distance2'] = $row['distance2'];
                $row_array['duration2'] = $row['duration2'];
                $row_array['distance3'] = $row['distance3'];
                $row_array['duration3'] = $row['duration3'];
                $row_array['distance4'] = $row['distance4'];
                $row_array['duration4'] = $row['duration4'];
                $row_array['distance5'] = $row['distance5'];
                $row_array['duration5'] = $row['duration5'];

                array_push($feed, $row_array);
                
            }
        
            return json_encode($feed); $result->free();
            
        } else { echo "Error: " . $query . "<br>" . $this->conn->error . "<br>";} $this->conn->close();
        
    }

    public function getSpecificRecord($table_name, $day) {

        $feed = array(); $query = "SELECT * FROM $table_name WHERE day = '$day'";

        if ($result = $this->conn->query($query)) {

            while ($row = $result->fetch_assoc()) {

                $row_array['day'] = $row['day'];
                $row_array['date'] = $row['date'];
                $row_array['time'] = $row['time'];
                $row_array['distance1'] = $row['distance1'];
                $row_array['duration1'] = $row['duration1'];
                $row_array['distance2'] = $row['distance2'];
                $row_array['duration2'] = $row['duration2'];
                $row_array['distance3'] = $row['distance3'];
                $row_array['duration3'] = $row['duration3'];
                $row_array['distance4'] = $row['distance4'];
                $row_array['duration4'] = $row['duration4'];
                $row_array['distance5'] = $row['distance5'];
                $row_array['duration5'] = $row['duration5'];

                array_push($feed, $row_array);
                
            }
        
            return json_encode($feed); $result->free();
            
        } else { echo "Error: " . $query . "<br>" . $this->conn->error . "<br>";} $this->conn->close();
        
    }

    public function getEtaGenerated($dateFrom, $dateTo) {
        $feed = array(); $query = "SELECT date, time, source, destination, eta1, error1, eta2, error2, eta3, error3 FROM eta_generated WHERE date BETWEEN '$dateFrom' AND '$dateTo'";

        if ($result = $this->conn->query($query)) {

            while ($row = $result->fetch_assoc()) {

                $row_array['date'] = $row['date'];
                $row_array['time'] = $row['time'];
                $row_array['source'] = $row['source'];
                $row_array['destination'] = $row['destination'];
                $row_array['eta1'] = $row['eta1'];
                $row_array['error1'] = $row['error1'];
                $row_array['eta2'] = $row['eta2'];
                $row_array['error2'] = $row['error2'];
                $row_array['eta3'] = $row['eta3'];
                $row_array['error3'] = $row['error3'];

                array_push($feed, $row_array);
                
            }
        
            return json_encode($feed); $result->free();
            
        } else { echo "Error: " . $query . "<br>" . $this->conn->error . "<br>";} $this->conn->close();
    }
    
    public function getFileUploaded($dateFrom, $dateTo) {
        $feed = array(); $query = "SELECT date, time, file_name, unique_record, duplicate_record, empty_record FROM file_uploaded WHERE date BETWEEN '$dateFrom' AND '$dateTo'";

        if ($result = $this->conn->query($query)) {

            while ($row = $result->fetch_assoc()) {

                $row_array['date'] = $row['date'];
                $row_array['time'] = $row['time'];
                $row_array['file_name'] = $row['file_name'];
                $row_array['unique_record'] = $row['unique_record'];
                $row_array['duplicate_record'] = $row['duplicate_record'];
                $row_array['empty_record'] = $row['empty_record'];

                array_push($feed, $row_array);
                
            }
        
            return json_encode($feed); $result->free();
            
        } else { echo "Error: " . $query . "<br>" . $this->conn->error . "<br>";} $this->conn->close();
    }
    
    public function isDataUnique($table_name, $date, $time) {
        
        $sql = "SELECT date, time FROM $table_name WHERE date = '$date' AND time = '$time'";

        $result = $this->conn->query($sql);

        return $result;
        
    }
}
