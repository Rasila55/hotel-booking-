<?php
$host = 'localhost';
$username = 'root';
$password = '' ;
$database = 'staymate';
$port = 3307;
$conn = mysqli_connect($host, $username,$password, $database, $port);

if($conn->connect_error){
    die("connection failed:".$conn->connect_error);

}
?>