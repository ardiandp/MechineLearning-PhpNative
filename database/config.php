<?php

// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "ml_php_native";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
