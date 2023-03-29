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

// Permissions: https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql
// Options: https://dev.mysql.com/doc/refman/8.0/en/privileges-provided.html#privileges-provided-summary
// "CREATE USER 'username'@'%' IDENTIFIED WITH mysql_native_password BY 'password'"; // create user with mysql_native_password to avoid PHP problems
// GRANT PRIVILEGE SELECT ON vidslide.user TO 'username'@'%'; // WITH GRANT OPTION => grant priviliges to others
// DROP USER 'username'@'%'; // delete user

// GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT, REFERENCES, RELOAD on *.* TO 'sammy'@'localhost' WITH GRANT OPTION;
// FLUSH PRIVILEGES; // save and reload, just to be sure - not needed
// REVOKE type_of_permission ON database_name.table_name FROM 'username'@'host'; // delete right
// SHOW GRANTS FOR 'username'@'host'; // display permissions
// DROP USER 'username'@'localhost'; // delete user

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT * FROM Post";
    $result = mysqli_query($conn, $sql);

    // Fetch all
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($data);

    // Free result set
    mysqli_free_result($result);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
}

// Disconnect
mysqli_close($conn);