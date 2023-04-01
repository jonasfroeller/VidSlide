<?php
// HEADERS
header("Access-Control-Allow-Origin: *"); // allow CORS
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization"); // Authorization => send token
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // OPTIONS => get available methods
header('Content-Type: application/json'); // return JSON

// phpinfo();

/* ---------- */

// INIT (MySQL-Database Cfg)
$host = 'host.docker.internal'; // database IP in docker container (127.0.0.1 (localhost) or other)
$root = 'root';
$pass = 'MEDTSSP';
$schema = 'vidslide';
$port = "3306";

/* ---------- */

// RESPONSE
$response = array(
    "data" => array(),
    "info" => array(
        "database_connection_details" => array(
            "database_username" => $root
        ),
        "fetch_method" => $_SERVER['REQUEST_METHOD']
    ),
    "log" => array(),
    "response" => "",
    "error" => false
);

// CONNECT
$connection = mysqli_connect($host, $root, $pass, "", $port); // $connection = mysqli_init(); + $connect_options = array(); + mysqli_real_connect(..., $connect_options (SSL and more));

/* ---------- */

// SETUP
if (!$connection) {
    errorOccurred($connection, $response, __LINE__);
} else {
    // Permissions: https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql
    // Options: https://dev.mysql.com/doc/refman/8.0/en/privileges-provided.html#privileges-provided-summary
    // delete user: DROP USER 'username'@'%';
    // delete right: REVOKE type_of_permission ON database_name.table_name FROM 'username'@'host'; 
    // display permissions: SHOW GRANTS FOR 'username'@'host'; 
    // save and reload: FLUSH; 

    // create user
    $guest_user_username = "guest";
    $guest_user_password = "420GUEST69";
    $guest_user = "CREATE USER IF NOT EXISTS '$guest_user_username'@'%' IDENTIFIED WITH mysql_native_password BY '$guest_user_password'"; // create user with mysql_native_password to avoid PHP problems
    $guest_user_query = mysqli_query($connection, $guest_user);

    if ($guest_user_query) {
        array_push($response["log"], "vidslide user created or the user existed already " . "[$guest_user_query]");
    } else {
        errorOccurred($connection, $response, __LINE__);
    }

    // TODO: FIX PRIVILEGES NOT UPDATING | disconnect and reconnect as guest user
    // get privileges
    $guest_user_privileges = "SELECT COUNT(*) as privileges FROM INFORMATION_SCHEMA.USER_PRIVILEGES WHERE GRANTEE = '$guest_user_username@%' AND PRIVILEGE_TYPE = 'SELECT'"; // select privileges of guest user
    $guest_user_privileges_query = mysqli_query($connection, $guest_user_privileges);
    $guest_user_privileges_row = mysqli_fetch_assoc($guest_user_privileges_query);
    $guest_user_privilege_count = $guest_user_privileges_row['privileges'];

    if ($guest_user_privilege_count == 0) {
        array_push($response["log"], "vidslide user privileges fetched " . "[$guest_user_privilege_count]");

        // free result set
        mysqli_free_result($guest_user_privileges_query);

        // create privileges
        $guest_user_grant_privileges = "GRANT SELECT ON *.* TO '$guest_user_username'@'%'"; // read only privileges // WITH GRANT OPTION => grant priviliges to others // PRIVILEGE => all privileges
        $guest_user_grant_privileges_query = mysqli_query($connection, $guest_user_grant_privileges);

        if ($guest_user_grant_privileges_query) {
            array_push($response["log"], "vidslide user privileges set or the privileges existed already " . "[$guest_user_grant_privileges_query]");
        } else {
            errorOccurred($connection, $response, __LINE__);
        }
    } else if ($guest_user_privilege_count == 1) {
        array_push($response["log"], "vidslide user privileges fetched " . "[$guest_user_privilege_count]");
    } else {
        errorOccurred($connection, $response, __LINE__);
    }

    // create database
    // DONT USE: https://dev.mysql.com/doc/refman/8.0/en/keywords.html
    $create_schema = "CREATE DATABASE IF NOT EXISTS $schema";
    $schema_query = mysqli_query($connection, $create_schema);

    if ($schema_query) {
        array_push($response["log"], "vidslide database created or the database existed already " . "[$schema_query]");
        mysqli_select_db($connection, $schema);
    } else {
        errorOccurred($connection, $response, __LINE__);
    }

    // create tables
    $table_01 = "CREATE TABLE IF NOT EXISTS USER (
        USER_ID INT AUTO_INCREMENT PRIMARY KEY,
        USER_USERNAME VARCHAR(25) NOT NULL,
        USER_PASSWORD VARCHAR(25) NOT NULL,
        USER_PROFILEPICTURE VARCHAR(100) DEFAULT NULL,
        USER_PROFILEDESCRIPTION VARCHAR(1000) DEFAULT NULL,
        USER_DATETIMECREATED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        USER_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT UNIQUE_USERNAME UNIQUE (USER_USERNAME)
    )";

    $table_02 = "CREATE TABLE IF NOT EXISTS VIDEO (
        VIDEO_ID INT AUTO_INCREMENT PRIMARY KEY,
        VIDEO_TITLE VARCHAR(25) NOT NULL,
        VIDEO_DESCRIPTION VARCHAR(500) DEFAULT NULL,
        VIDEO_VIEWS INT DEFAULT 0,
        VIDEO_SHARES INT DEFAULT 0,
        VIDEO_DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        VIDEO_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        USER_ID INT NOT NULL,
        FOREIGN KEY (USER_ID) REFERENCES USER(USER_ID)
    )";

    $table_03 = "CREATE TABLE IF NOT EXISTS USER_SOCIAL (
        SOCIAL_ID INT AUTO_INCREMENT PRIMARY KEY,
        SOCIAL_PLATFORM VARCHAR(25) NOT NULL,
        SOCIAL_URL VARCHAR(250) NOT NULL,
        USER_ID INT NOT NULL,
        FOREIGN KEY (USER_ID) REFERENCES USER(USER_ID),
        CONSTRAINT UNIQUE_SOCIAL UNIQUE (SOCIAL_PLATFORM, SOCIAL_URL, USER_ID)
    )";

    $table_04 = "CREATE TABLE IF NOT EXISTS USER_FOLLOWING (
        FOLLOWING_ID INT AUTO_INCREMENT PRIMARY KEY,
        FOLLOWING_SUBSCRIBER INT NOT NULL,
        FOLLOWING_SUBSCRIBED INT NOT NULL,
        FOREIGN KEY (FOLLOWING_SUBSCRIBER) REFERENCES USER(USER_ID),
        FOREIGN KEY (FOLLOWING_SUBSCRIBED) REFERENCES USER(USER_ID),
        CONSTRAINT UNIQUE_FOLLOWING UNIQUE (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED)
    )";

    $table_05 = "CREATE TABLE IF NOT EXISTS VIDEO_FEEDBACK (
        VIDEO_FEEDBACK_ID INT AUTO_INCREMENT PRIMARY KEY,
        VIDEO_FEEDBACK_TYPE ENUM('positive', 'negative') NOT NULL,
        VIDEO_ID INT NOT NULL,
        USER_ID INT NOT NULL,
        FOREIGN KEY (VIDEO_ID) REFERENCES VIDEO(VIDEO_ID),
        FOREIGN KEY (USER_ID) REFERENCES USER(USER_ID),
        CONSTRAINT UNIQUE_VIDEO_FEEDBACK UNIQUE (VIDEO_ID, USER_ID)
    )";

    $table_06 = "CREATE TABLE IF NOT EXISTS VIDEO_COMMENT (
        COMMENT_ID INT AUTO_INCREMENT PRIMARY KEY,
        COMMENT_PARENT_ID INT DEFAULT NULL,
        COMMENT_MESSAGE VARCHAR(250) NOT NULL,
        COMMENT_DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        COMMENT_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        VIDEO_ID INT NOT NULL,
        USER_ID INT NOT NULL,
        FOREIGN KEY (VIDEO_ID) REFERENCES VIDEO(VIDEO_ID),
        FOREIGN KEY (USER_ID) REFERENCES USER(USER_ID)
    )";

    $table_07 = "CREATE TABLE IF NOT EXISTS COMMENT_FEEDBACK (
        COMMENT_FEEDBACK_ID INT AUTO_INCREMENT PRIMARY KEY,
        COMMENT_FEEDBACK_TYPE ENUM('positive', 'negative') NOT NULL,
        COMMENT_ID INT NOT NULL,
        USER_ID INT NOT NULL,
        FOREIGN KEY (COMMENT_ID) REFERENCES VIDEO_COMMENT(COMMENT_ID),
        CONSTRAINT UNIQUE_COMMENT_FEEDBACK UNIQUE (COMMENT_ID, USER_ID)
    )";

    $table_08 = "CREATE TABLE IF NOT EXISTS VIDEO_HASHTAG (
        HASHTAG_ID INT AUTO_INCREMENT PRIMARY KEY,
        HASHTAG_NAME VARCHAR(500) NOT NULL,
        VIDEO_ID INT NOT NULL,
        FOREIGN KEY (VIDEO_ID) REFERENCES VIDEO(VIDEO_ID),
        CONSTRAINT UNIQUE_HASHTAG UNIQUE (VIDEO_ID, HASHTAG_NAME)
    )";

    for ($i = 1; $i <= 8; $i++) {
        $table_create_query = mysqli_query($connection, ${"table_" . str_pad($i, 2, "0", STR_PAD_LEFT)});
        if ($table_create_query) {
            array_push($response["log"], "table " . $i . " created successfully or the table existed already");
        } else {
            errorOccurred($connection, $response, __LINE__);
        }
    }

    // mock data
    insertMockData($connection);

    // check if server is alive
    if (mysqli_ping($connection)) {
        array_push($response["log"], "connection is ok!");
    } else {
        errorOccurred($connection, $response, __LINE__);
    }

    // login as guest => READ ONLY
    mysqli_close($connection);
    $connection = mysqli_connect($host, $guest_user_username, $guest_user_password, $schema, $port);
}

/* ---------- */

// REQUEST METHODS | GET, POST, PUT, DELETE STATEMENTS
if ($_SERVER['REQUEST_METHOD'] === 'GET') { // no private data (password is hashed)
    if (isset($_GET["user"])) {
        $user = mysqli_real_escape_string($connection, $_GET["user"]);
        if ($user == "all") {
            $table_user = "SELECT * FROM USER";
            $response = getUser($connection, $response, $table_user, $user, false);
        } else {
            $userID = intval($user); // check if exists => SELECT COUNT(*) as count FROM USER WHERE UID = $userID;
            $userID_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM USER WHERE USER_ID = ?");
            mysqli_stmt_bind_param($userID_exists, 'i', $userID);
            mysqli_stmt_execute($userID_exists);
            $user_query = mysqli_stmt_get_result($userID_exists);
            $user_rows = mysqli_fetch_all($user_query, MYSQLI_ASSOC);

            if ($user_rows[0]["count"] !== 0) {
                $table_user = mysqli_prepare($connection, 'SELECT * FROM USER WHERE USER_ID = ?');
                $response = getUser($connection, $response, $table_user, $userID, true);
            } else {
                errorOccurred($connection, $response, __LINE__, "user not found");
            }
        }
    }

    if (isset($_GET["video"])) {
        $video = mysqli_real_escape_string($connection, $_GET["video"]);
        if ($video == "all") {
            $table_video = "SELECT * FROM VIDEO";
            $response = getUser($connection, $response, $table_video, $video, false);
        } else if ($video == "random") {
            $table_video = "SELECT * FROM VIDEO ORDER BY RAND() LIMIT 1"; // inefficient
            $response = getVideo($connection, $response, $table_video, $video, false);
        } else {
            $videoID = intval($video);
            if ($videoID !== 0) {
                $table_video = mysqli_prepare($connection, 'SELECT * FROM VIDEO WHERE VIDEO_ID = ?');
                $response = getVideo($connection, $response, $table_video, $videoID, true);
            } else {
                errorOccurred($connection, $response, __LINE__);
            }
        }
    }

    if (isset($_GET["comments"])) {
        $videoID = intval(mysqli_real_escape_string($connection, $_GET["comments"]));
        $table_comment = "SELECT * FROM VIDEO_COMMENT WHERE VIDEO_ID = ?";
        $response = getComments($response, $table_comment, $videoID);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["action"])) {
        $action = $_POST["action"];
        if ($action == "signup") {
        } else if ($action == "signin") {
            if (isset($_POST["username"]) && isset($_POST["password"])) {
                $username = mysqli_real_escape_string($connection, $_POST["username"]);
                $password = mysqli_real_escape_string($connection, $_POST["password"]);
                $response["info"]["database_connection_details"]["database_username"] = $username;
            }
        } else if ($action == "signout") {
        } else if ($action == "video") {
        } else if ($action == "comment") {
        } else if ($action == "like") {
        } else if ($action == "dislike") {
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (isset($_POST["medium"])) {
        $medium = $_POST["medium"];
        if ($medium == "profile_username") {
        } else if ($medium == "profile_password") {
        } else if ($medium == "profile_description") {
        } else if ($medium == "profile_socials") {
        } else if ($medium == "profile_picture") {
        } else if ($medium == "video_post_title") {
        } else if ($medium == "video_post_description") {
        } else if ($medium == "video_post_hashtags") {
        } else if ($medium == "comment_post_text") {
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_POST["medium"])) {
        $medium = $_POST["medium"];
        if ($medium == "all") {
        } else if ($medium == "account") {
        } else if ($medium == "video") {
        } else if ($medium == "comment") {
        }
    }
}

/* ---------- */

// FUNCTIONS
function errorOccurred($connection, $response, $line, $message = null)
{
    $response["error"] = ($message ?? "") . " (error occurred at line " . $line . " [" . mysqli_connect_errno() . ";" .  mysqli_connect_error() . "])";
    // Disconnect
    mysqli_close($connection); // mysqli_kill() => close fast | $thread_id = mysqli_thread_id($con); mysqli_kill($con, $thread_id);
    // Log Response
    echo json_encode($response);
    exit(); // die()
}

function getUser($connection, $response, $table_user, $userID, $prepare)
{
    if ($prepare) {
        // bind values
        mysqli_stmt_bind_param($table_user, 'i', $userID);
        // execute query
        mysqli_stmt_execute($table_user);
        // fetch result
        $user_query = mysqli_stmt_get_result($table_user);
        $user_rows = mysqli_fetch_all($user_query, MYSQLI_ASSOC);
        $user_response = json_encode($user_rows);
    } else {
        // execute query
        $user_query = mysqli_query($connection, $table_user); // SELECT, SHOW, DESCRIBE or EXPLAIN returns mysqli_result object // mysqli_real_query() => doesn't wait for response // mysqli_reap_async_query() => async
        // fetch all
        $user_rows = mysqli_fetch_all($user_query, MYSQLI_ASSOC);
        $user_response = json_encode($user_rows);
    }

    // save as response data
    $response["response"] = $response["response"] . "READ user table [$prepare];"; // res;res;res
    array_push($response["data"], $user_response);
    // free result set
    mysqli_free_result($user_query);
    return $response;
}

function getVideo($connection, $response, $table_video, $videoID, $prepare)
{
    if ($prepare) {
        // bind values
        mysqli_stmt_bind_param($table_video, 'i', $videoID);
        // execute query
        mysqli_stmt_execute($table_video);
        // fetch result
        $video_query = mysqli_stmt_get_result($table_video);
        $video_rows = mysqli_fetch_all($video_query, MYSQLI_ASSOC);
        $video_response = json_encode($video_rows);
    } else {
        // excecute query
        $video_query = mysqli_query($connection, $table_video); // SELECT, SHOW, DESCRIBE or EXPLAIN returns mysqli_result object
        // fetch all
        $video_rows = mysqli_fetch_all($video_query, MYSQLI_ASSOC);
        $video_response = json_encode($video_rows);
    }

    // save as response data
    $response["response"] = $response["response"] . "READ video table [$prepare];"; // res;res;res
    array_push($response["data"], $video_response);
    // free result set
    mysqli_free_result($video_query);
    return $response;
}

function getComments($response, $table_comment, $videoID)
{
    // bind values
    mysqli_stmt_bind_param($table_comment, 'i', $videoID);
    // execute query
    mysqli_stmt_execute($table_comment);
    // fetch result
    $comment_query = mysqli_stmt_get_result($table_comment);
    $comment_rows = mysqli_fetch_all($comment_query, MYSQLI_ASSOC);
    $comment_response = json_encode($comment_rows);

    // save as response data
    $response["response"] = $response["response"] . "READ user table;"; // res;res;res
    array_push($response["data"], $comment_response);
    // free result set
    mysqli_free_result($comment_query);
    return $response;
}

function insertMockData($connection)
{
    // SELECT COUNT(*) FROM table_name; returns 0 => empty || IGNORE => ignores if UNIQUE

    $mock_user =
        "INSERT IGNORE INTO USER (USER_USERNAME, USER_PASSWORD, USER_PROFILEPICTURE, USER_PROFILEDESCRIPTION) VALUES ('maxmustermann', 'passwort123', 'https://example.com/profilepic1.jpg', 'Ich bin Max und ich liebe es, Videos zu machen!');

        INSERT IGNORE INTO VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, USER_ID) VALUES ('Mein erster Vlog', 'Hier ist mein erster Vlog, den ich jemals gemacht habe!', 1);
    
        INSERT IGNORE INTO USER_SOCIAL (SOCIAL_PLATFORM, SOCIAL_URL, USER_ID) VALUES ('Twitter', 'https://twitter.com/maxmustermann', 1);
        
        INSERT IGNORE INTO USER_FOLLOWING (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED) VALUES (1, 1);
        
        INSERT IGNORE INTO VIDEO_FEEDBACK (VIDEO_FEEDBACK_TYPE, VIDEO_ID, USER_ID) VALUES ('positive', 1, 1);
        
        INSERT IGNORE INTO VIDEO_HASHTAG (HASHTAG_NAME, VIDEO_ID) VALUES ('Vlog', 1);
        
        INSERT IGNORE INTO VIDEO_COMMENT (COMMENT_MESSAGE, VIDEO_ID, USER_ID) VALUES ('Tolles Video!', 1, 1);
        
        INSERT IGNORE INTO COMMENT_FEEDBACK (COMMENT_FEEDBACK_TYPE, COMMENT_ID, USER_ID) VALUES ('negative', 1, 1);";

    mysqli_multi_query($connection, $mock_user);
}

/* ---------- */

// CLEAN UP
// Log Response
echo json_encode($response);

// Disconnect
mysqli_close($connection);

/* ---------- */

// SCRIPT END
exit();
