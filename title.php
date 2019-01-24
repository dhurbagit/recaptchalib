<?php


ob_start();
session_start();
error_reporting(E_ERROR);
include "data/conn.php";
$conn = new Dbconn();
$conn -> Connection();


?>