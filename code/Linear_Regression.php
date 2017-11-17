<?php

class Linear_Regression {
    
    private $conn;
    private $x;
    private $y;
    private $xMeanofXdifference;
    private $yMeanofYdifference;
    private $predictedY;
    private $meanofX;
    private $meanofY;
    private $B0;
    private $B1;
    private $real_value;
    private $e;
    private $flag;

    // constructor
    function __construct() {
        require_once 'include/DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {
        
    }
    
    public function assignVariables($x, $y, $value, $flag) {
        $this->x = $x;
        $this->y = $y;
        $this->predictedY = array();
        $this->meanofX = 0.0;
        $this->meanofY = 0.0;
        $this->B0 = 0.0;
        $this->B1 = 0.0;
        $this->e = 0.0;
        $this->flag = $flag;
        
        $this->run($value);
    }
    
    public function run($value) {
        
        $this->meanofX = $this->getMeanValue($this->x);
        
        $this->meanofY = $this->getMeanValue($this->y);
        
        $this->xMeanofXdifference = $this->getDifferenceXY($this->x, $this->meanofX);
        
        $this->yMeanofYdifference = $this->getDifferenceXY($this->y, $this->meanofY);
        
        $this->B1 = $this->findB1();
        
        $this->B0 = $this->findB0();
        
        $this->predictedY = $this->predictY();
        
        $this->e = $this->errorRate();
        
        $this->real_value = ($this->B0 + ($this->B1 * $value));
        
        if ($this->flag == FALSE) {
        
            echo "<b>Estimated Time : </b>" . ceil($this->real_value) . " mins";
        
            echo " (+-" . round($this->e, 2) . " mins)<br>";
            
        } else {
            echo "<b>Estimated Time : </b>" . ceil($this->real_value * .8) . " mins";
        
            echo " (+-" . round($this->e, 2) . " mins)<br>";
        }
        
    }
    
    public function getMeanValue($list) {
        
        $sum = 0.0;
        
        for ($c = 0; $c < count($list); $c++) {
            $sum += $list[$c];
        }
        
        $meanValue = $sum/count($list);
        
        return $meanValue;
    }
    
    public function getDifferenceXY($x, $y) {
        
        $list = array();
        
        for ($c = 0; $c < count($x); $c++) {
            array_push($list, $x[$c] - $y);
        }
        
        return $list;
        
    }
    
    public function findB1() {
        
        $sum1 = 0.0;
        $sum2 = 0.0;
        
        for ($c = 0; $c < count($this->xMeanofXdifference); $c++) {
            $sum1 += ($this->xMeanofXdifference[$c] * $this->yMeanofYdifference[$c]);
        }
        
        for ($c = 0; $c < count($this->xMeanofXdifference); $c++) {
            $sum2 += ($this->xMeanofXdifference[$c] * $this->xMeanofXdifference[$c]);
        }
        
        return $sum1/$sum2;
        
    }
    
    public function findB0() {
        return $this->meanofY - ($this->B1 * $this->meanofX);
    }
    
    public function predictY() {
        
        for ($c = 0; $c < count($this->x); $c++) {
            array_push($this->predictedY, $this->B0 + ($this->B1 * $this->x[$c]));
        }
        
        return $this->predictedY;
        
    }
    
    public function errorRate() {
        
        $sum = 0.0;
        
        for ($c = 0; $c < count($this->predictedY); $c++) {
            $sum += pow($this->predictedY[$c] - $this->y[$c], 2);
        }
        
        return sqrt($sum/count($this->predictedY));
        
    }
    
    public function getEta () {
        return ceil($this->real_value);
    }
    
    public function getError () {
        return round($this->e, 2);
    }
    
}

?>

