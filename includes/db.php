<?php 
session_start();
$servername="localhost";
$user="root";
$password="King#123";
$dbname="api";

$conn = mysqli_connect($servername,$user,$password,$dbname);
if(!$conn){
    die("Failed Connectin".mysqli_connect_error());
}

?>