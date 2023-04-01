<?php
// phpinfo();
header("Access-Control-Allow-Origin: *"); // allow CORS
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization"); // Authorization => send token
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // OPTIONS => get available methods
header('Content-Type: application/json'); // return JSON

// INIT (MySQL-Database Cfg)
$hostname = 'host.docker.internal'; // database IP in docker container (127.0.0.1 (localhost) or other)
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
} else {
    $username = 'root';
    $password = 'MEDTSSP';
}
$databaseschema = 'vidslide';
$port = "3306";

$response = array(
    "data" => array(),
    "info" => array(
        "database_connection_details" => array(
            "database_username" => $username
        ),
        "fetch_method" => $_SERVER['REQUEST_METHOD']
    ),
    "log" => array(),
    "response" => "",
    "error" => false
);

// CONNECT
$connection = mysqli_connect($hostname, $username, $password, "", $port);

// SETUP
if (!$connection) {
    $response["error"] = mysqli_connect_error();
    echo json_encode($response);
    exit(); // die()
} else {


    $schema = "CREATE DATABASE IF NOT EXISTS $databaseschema";

    if (mysqli_query($connection, $schema)) {
        array_push($response["log"], "vidslide db created");
        mysqli_select_db($connection, $databaseschema);
    } else {
        $response["error"] = mysqli_connect_error();
        echo json_encode($response);
        exit(); // die()
    }

    $table_01 = "CREATE TABLE IF NOT EXISTS USER (
        UID INT AUTO_INCREMENT PRIMARY KEY,
        USERNAME VARCHAR(25) NOT NULL,
        PASSWORD VARCHAR(25) NOT NULL,
        DATETIMECREATED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PROFILEPICTURE VARCHAR(100) DEFAULT NULL,
        PROFILEDESCRIPTION VARCHAR(1000) DEFAULT NULL
    )";

    $table_02 = "CREATE TABLE IF NOT EXISTS VIDEO (
        VID INT AUTO_INCREMENT PRIMARY KEY,
        UID INT NOT NULL,
        TITLE VARCHAR(25) NOT NULL,
        DESCRIPTION VARCHAR(500) DEFAULT NULL,
        DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        VID_VIEWS INT DEFAULT 0,
        SHARES INT DEFAULT 0,
        FOREIGN KEY (UID) REFERENCES USER(UID)
    )";

    $table_03 = "CREATE TABLE IF NOT EXISTS USER_SOCIAL (
        SID INT AUTO_INCREMENT PRIMARY KEY,
        UID INT NOT NULL,
        PLATFORM VARCHAR(25) NOT NULL,
        URL VARCHAR(250) NOT NULL,
        FOREIGN KEY (UID) REFERENCES USER(UID)
    )";

    $table_04 = "CREATE TABLE IF NOT EXISTS USER_FOLLOWING (
        FID INT AUTO_INCREMENT PRIMARY KEY,
        FOLLOWER INT NOT NULL,
        FOLLOWING INT NOT NULL,
        FOREIGN KEY (FOLLOWER) REFERENCES USER(UID),
        FOREIGN KEY (FOLLOWING) REFERENCES USER(UID),
        CONSTRAINT UNIQUE_FOLLOWING UNIQUE (FOLLOWER, FOLLOWING)
    )";

    $table_05 = "CREATE TABLE IF NOT EXISTS VIDEO_FEEDBACK (
        FID INT AUTO_INCREMENT PRIMARY KEY,
        VID INT NOT NULL,
        FB_TYPE ENUM('positive', 'negative') NOT NULL,
        FOREIGN KEY (VID) REFERENCES VIDEO(VID)
    )";

    $table_06 = "CREATE TABLE IF NOT EXISTS VIDEO_HASHTAG (
        HID INT AUTO_INCREMENT PRIMARY KEY,
        VID INT NOT NULL,
        HT_NAME VARCHAR(500) NOT NULL,
        FOREIGN KEY (VID) REFERENCES VIDEO(VID),
        CONSTRAINT UNIQUE_HASHTAG UNIQUE (VID, HT_NAME)
    )";

    $table_07 = "CREATE TABLE IF NOT EXISTS VIDEO_COMMENT (
        CID INT AUTO_INCREMENT PRIMARY KEY,
        PARENTCID INT,
        VID INT NOT NULL,
        UID INT NOT NULL,
        DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        TEXTCONTENT VARCHAR(250) DEFAULT NULL,
        LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (VID) REFERENCES VIDEO(VID)
    )";

    for ($i = 1; $i <= 7; $i++) {
        if (mysqli_query($connection, ${"table_" . str_pad($i, 2, "0", STR_PAD_LEFT)})) {
            array_push($response["log"], "table " . $i . " created successfully or the table existed already");
        } else {
            $response["error"] = mysqli_connect_error();
            echo json_encode($response);
            exit(); // die()
        }
    }

    // echo json_encode($response);
}

function getUser($connection, $response, $table_user)
{
    $user_query = mysqli_query($connection, $table_user); // SELECT, SHOW, DESCRIBE or EXPLAIN returns mysqli_result object

    // fetch all
    $user_rows = mysqli_fetch_all($user_query, MYSQLI_ASSOC);

    // save as response data
    $response["response"] = $response["response"] . "READ user table;"; // res;res;res
    array_push($response["data"], json_encode($user_rows));

    // free result set
    mysqli_free_result($user_query);

    return $response;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') { // no private data (password is hashed)
    if (isset($_GET["user"])) {
        if ($_GET["user"] === "all") {
            $table_user = "SELECT * FROM USER";
            $response = getUser($connection, $response, $table_user);
        } else {
            $userID = intval($_GET["user"]);
            if ($userID !== 0) {
                $table_user = "SELECT * FROM USER WHERE UID = $userID";
                $response = getUser($connection, $response, $table_user);
            } else {
                $response["error"] = "failed to parse userID";
                echo json_encode($response);
                exit(); // die()
            }
        }
    }
}

echo json_encode($response);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
}

// Disconnect
mysqli_close($connection);

// TODO: USERS
// if no user specified use user that can only read on the database
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

function insertMockData()
{
    /* SELECT COUNT(*) FROM table_name; returns 0 => empty

    INSERT INTO USER (
        USERNAME,
        PASSWORD,
        PROFILEPICTURE,
        PROFILEDESCRIPTION
    ) VALUES (
        "JohnDoe",
        "pass123",
        "https://picsum.photos/50/50",
        "I'm a software engineer"
    ),
    (
        "JaneDoe",
        "qwerty123",
        "https://picsum.photos/50/50",
        "I love to dance and create videos"
    ),
    (
        "PeterPan",
        "ilovefairytales",
        "https://picsum.photos/50/50",
        NULL
    ),
    (
        "MaryPoppins",
        "supercalifragilisticexpialidocious",
        NULL,
        "Practically perfect in every way"
    );
    
    INSERT INTO VIDEO (
        UID,
        TITLE,
        DESCRIPTION
    ) VALUES (
        1,
        "My First Video",
        "Just testing out this new platform!"
    ),
    (
        2,
        "Dancing to my favorite song",
        "I can't stop listening to this song and wanted to share my dance with you all!"
    ),
    (
        3,
        "How to Fly with Peter Pan",
        "Join me on a magical journey to Neverland!"
    ),
    (
        4,
        "Cleaning up the nursery with Mary Poppins",
        "A spoonful of sugar helps the medicine go down!"
    ),
    (
        1,
        "Reacting to my first video",
        "I can't believe how far I've come!"
    );
    
    INSERT INTO USER_SOCIAL (
        UID,
        PLATFORM,
        URL
    ) VALUES (
        1,
        "GitHub",
        "https://github.com/johndoe"
    ),
    (
        2,
        "GitHub",
        "https://github.com/janedoe"
    ),
    (
        3,
        "GitHub",
        "https://github.com/peterpan"
    ),
    (
        4,
        "GitHub",
        "https://github.com/marypoppins"
    );
    
    INSERT INTO USER_FOLLOWING (
        FOLLOWER,
        FOLLOWING
    ) VALUES (
        1,
        2
    ),
    (
        1,
        3
    ),
    (
        2,
        1
    ),
    (
        2,
        4
    ),
    (
        3,
        1
    ),
    (
        3,
        4
    ),
    (
        4,
        2
    ),
    (
        4,
        3
    );
    
    INSERT INTO VIDEO_FEEDBACK (
        VID,
        FB_TYPE
    ) VALUES (
        1,
        "positive"
    ),
    (
        1,
        "negative"
    ),
    (
        2,
        "positive"
    ),
    (
        2,
        "positive"
    ),
    (
        2,
        "negative"
    ),
    (
        3,
        "positive"
    ),
    (
        4,
        "positive"
    );
    
    INSERT INTO VIDEO_HASHTAG (
        VID,
        HT_NAME
    ) VALUES (
        1,
        "firstvideo"
    ),
    (
        2,
        "dance"
    ),
    (
        2,
        "favorite"
    ),
    (
        3,
        "neverland"
    ),
    (
        3,
        "flying"
    ),
    (
        4,
        "cleaning"
    ),
    (
        4,
        "nursery"
    );
    
    INSERT INTO VIDEO_COMMENT (
        PARENTCID,
        VID,
        UID,
        TEXTCONTENT
    ) VALUES (
        NULL,
        1,
        2,
        "Great job on your first video, John!"
    ),
    (
        NULL,
        1,
        3,
        "I think you have a lot of potential on this platform"
    ),
    (
        1,
        1,
        1,
        "Thanks for the feedback, Jane!"
    ),
    (
        2,
        1,
        2,
        "Sorry, John, I didn't mean to come across as harsh"
    ),
    (
        NULL,
        2,
        1,
        "I loved your dance, Jane!"
    ),
    (
        NULL,
        3,
        4,
        "This is such a fun video, Mary!"
    ),
    (
        NULL,
        4,
        2,
        "I wish Mary Poppins would come clean my house!"
    ),
    (
        NULL,
        4,
        3,
        "Haha, me too!"
    ) */
}
