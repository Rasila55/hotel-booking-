<?php
$host = 'localhost';
$username = 'root';
$password = '' ;
$database = 'staymate';
$port = 3306;
$conn = mysqli_connect($host, $username,$password, $database, $port);

if($conn->connect_error){
    die("connection failed:".$conn->connect_error);

}

function getDBConnection()
{
    global $conn;
    return $conn;
}
?>