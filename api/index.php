<?php

header("Access-Control-Allow-Origin: *"); // Allow CORS
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Content-Type: application/json'); // Return JSON

// phpinfo();

// MySQL-Database Cfg
$hostname = 'host.docker.internal'; // Database IP in docker container (127.0.0.1 (localhost) or other)
if (isset($_GET["username"]) && isset($_GET["password"])) {
    $username = $_GET["username"];
    $password = $_GET["password"];
} else {
    $username = 'root';
    $password = 'MEDTSSP';
}
if (isset($_GET["schema"])) {
    $databaseschema = $_GET["schema"];
} else {
    $databaseschema = 'vidslide';
}
$port = "3306";

// Connect
$conn = mysqli_connect($hostname, $username, $password, $databaseschema, $port);

// Check If Succesfull
if (!$conn) { // if no user specified use user that can only read on the database
    die("Connection failed: " . mysqli_connect_error()); // exit();
}

// "CREATE USER 'username'@'%' IDENTIFIED WITH mysql_native_password BY 'password'"; // create user with mysql_native_password to avoid PHP problems
// GRANT PRIVILEGE ON database.table TO 'username'@'%'; // grant priviliges
// DROP USER 'username'@'%'; // delete user

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT * FROM sys_config";
    $result = mysqli_query($conn, $sql);

    // Fetch all
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($data);

    // Free result set
    mysqli_free_result($result);
}

// Disconnect
mysqli_close($conn);