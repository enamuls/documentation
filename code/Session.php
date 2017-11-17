<?php

include("include/Config.php");
require_once 'include/DB_Connect.php';

// connecting to database
$db = new Db_Connect();
$conn = $db->connect();
session_start();

$user_check = $_SESSION['login_user'];

$ses_sql = mysqli_query($conn, "SELECT username FROM user WHERE username = '$user_check' ");

$row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC);

$login_session = $row['username'];

if (!isset($_SESSION['login_user'])) {
    header('Location: http://operation3inc.com/wp-admin/CSE5ITP/Login.php');
}
?>