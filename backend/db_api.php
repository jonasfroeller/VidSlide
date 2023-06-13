<?php

// INFO: 
// Permissions: 
//  - https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql
//  - Options: https://dev.mysql.com/doc/refman/8.0/en/privileges-provided.html#privileges-provided-summary
// Tables:
//  - Don't use in table names and attributes: https://dev.mysql.com/doc/refman/8.0/en/keywords.html

// phpinfo();
// Jump To: TODO (daily tasks), BUG (!!!), IMPROVE (would make things cleaner), REMOVE (only for dev)

declare(strict_types=1);

// HEADERS
header("Access-Control-Allow-Origin: *"); // allow CORS
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization"); // Authorization => send token
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // OPTIONS => get available methods
header('Content-Type: application/json'); // return JSON

// Authentication
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
// Environment Variables
use Dotenv\Dotenv;
// Video Processing
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;

require_once('./vendor/autoload.php');

/* ---------- */

// FUNCTIONS
// GENERAL FUNCTIONS

/**
 * Returns errors. Fatal Errors will exit the script.
 */
function errorOccurred($connection, $response, $line, $message = null, $fatal = false)
{
    if ($response["error"] != null) {
        $response["error"] = $response["error"] . " | " . ($message ?? "") . " (error occurred at line " . $line . " [" . mysqli_connect_errno() . ";" .  mysqli_connect_error() . "])";
    } else {
        $response["error"] = ($message ?? "") . " (error occurred at line " . $line . " [" . mysqli_connect_errno() . ";" .  mysqli_connect_error() . "])";
    }

    if ($fatal) {
        // Disconnect
        mysqli_close($connection); // mysqli_kill() => close fast | $thread_id = mysqli_thread_id($con); mysqli_kill($con, $thread_id);

        echo json_encode($response); // Log Response
        exit(); // die()
    } else {
        return $response;
    }
}

// VALIDATION FUNCTIONS

/**
 * Returns JWT for client.
 */
function sendJWT($payload, $privateKey)
{
    $jwt = JWT::encode($payload, $privateKey, 'RS256');
    return $jwt;
}

/**
 * Gets and validates JWT from client request. Returns its data as JSON.
 */
function getJWT($connection, $response, $jwt, $publicKey)
{
    try {
        $token = JWT::decode($jwt, new Key($publicKey, 'RS256'));
        return json_decode(json_encode($token), true); // (array) casts to assoc array
    } catch (InvalidArgumentException $e) {
        errorOccurred($connection, $response, __LINE__, "provided key/key-array is empty or malformed.", true);
    } catch (DomainException $e) {
        errorOccurred($connection, $response, __LINE__, "provided algorithm is unsupported OR provided key is invalid OR unknown error thrown in openSSL or libsodium OR libsodium is required but not available.", true);
    } catch (SignatureInvalidException $e) {
        errorOccurred($connection, $response, __LINE__, "provided JWT signature verification failed.", true);
    } catch (BeforeValidException $e) {
        errorOccurred($connection, $response, __LINE__, "provided JWT is trying to be used before 'nbf' claim OR provided JWT is trying to be used before 'iat' claim.", true);
    } catch (ExpiredException $e) {
        errorOccurred($connection, $response, __LINE__, "provided JWT is trying to be used after 'exp' claim.", true);
    } catch (UnexpectedValueException $e) {
        errorOccurred($connection, $response, __LINE__, "provided JWT is malformed OR provided JWT is missing an algorithm / using an unsupported algorithm OR provided JWT algorithm does not match provided key OR provided key ID in key/key-array is empty or invalid.", true);
    }
}

// DATABASE FUNCTIONS

function doesntHurtConstraint($connection, $table_name, $bind_str, $return_type, ...$bind_vars)
{
    if ($table_name == "VS_USER_FOLLOWING") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_USER_FOLLOWING WHERE FOLLOWING_SUBSCRIBER = ? AND FOLLOWING_SUBSCRIBED = ?");
    } else if ($table_name == "VS_VIDEO_FEEDBACK") {
        $id_exists = mysqli_prepare($connection, "SELECT * FROM VS_VIDEO_FEEDBACK WHERE VS_VIDEO_ID = ? AND VS_USER_ID = ?");
    } else if ($table_name == "VS_COMMENT_FEEDBACK") {
        $id_exists = mysqli_prepare($connection, "SELECT * FROM VS_COMMENT_FEEDBACK WHERE COMMENT_ID = ? AND VS_USER_ID = ?");
    }

    mysqli_stmt_bind_param($id_exists, $bind_str, ...$bind_vars);
    mysqli_stmt_execute($id_exists);
    $query = mysqli_stmt_get_result($id_exists);
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
    mysqli_stmt_close($id_exists);

    if ($return_type == "object") {
        return $result;
    } else {
        return $result[0]["count"] == 0;
    }
}

/**
 * Looks for a medium in the database. Returns true if it exists, false otherwise.
 */
function checkIfIdExists($connection, $type, $id, $bind_type = "i")
{
    if ($type == "user") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_USER WHERE VS_USER_ID = ?");
    } else if ($type == "user_username") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_USER WHERE USER_USERNAME = ?");
    } else if ($type == "video") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO WHERE VS_VIDEO_ID = ?");
    } else if ($type == "video_userID") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO WHERE VS_USER_ID = ?");
    } else if ($type == "video_title") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO WHERE VIDEO_TITLE LIKE ?");
    } else if ($type == "video_tag") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO WHERE VS_VIDEO_ID IN (SELECT VS_VIDEO_ID FROM VS_VIDEO_HASHTAG WHERE HASHTAG_NAME LIKE ?)");
    } else if ($type == "video_username") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO WHERE VS_USER_ID IN (SELECT VS_USER_ID FROM VS_USER WHERE USER_USERNAME LIKE ?)");
    } else if ($type == "comments") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO_COMMENT WHERE VS_VIDEO_ID = ?");
    } else if ($type == "comment_feedback") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_COMMENT_FEEDBACK JOIN VS_VIDEO_COMMENT ON VS_COMMENT_FEEDBACK.COMMENT_ID = VS_VIDEO_COMMENT.COMMENT_ID WHERE VS_VIDEO_COMMENT.VS_VIDEO_ID = ?");
    } else if ($type == "feedback_videoID") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO_FEEDBACK WHERE VS_VIDEO_ID = ?");
    } else if ($type == "tags_videoID") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO_HASHTAG WHERE VS_VIDEO_ID = ?");
    } else if ($type == "tags_name") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_VIDEO_HASHTAG WHERE HASHTAG_NAME = ?");
    } else { // fallback
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VS_USER WHERE VS_USER_ID = ?");
    }

    mysqli_stmt_bind_param($id_exists, $bind_type, $id);
    mysqli_stmt_execute($id_exists);
    $query = mysqli_stmt_get_result($id_exists);
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
    mysqli_stmt_close($id_exists);

    return $result[0]["count"] != 0;
}

/**
 * Gets a medium from the database. 
 */
function getMedium($connection, $response, $table, $id = 0, $prepare = false, $bind_type = "i", $onlyRes = false)
{
    if ($prepare) {
        // bind values
        mysqli_stmt_bind_param($table, $bind_type, $id);
        // execute query
        mysqli_stmt_execute($table);
        // fetch result
        $query = mysqli_stmt_get_result($table);
        $rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $res = $rows;
        mysqli_stmt_close($table);
    } else {
        // execute query
        $query = mysqli_query($connection, $table); // SELECT, SHOW, DESCRIBE or EXPLAIN returns mysqli_result object // mysqli_real_query() => doesn't wait for response // mysqli_reap_async_query() => async
        // fetch all
        $rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $res = $rows;
    }

    // save as response data
    if (!$onlyRes) {
        $prepare_state = $prepare ? $prepare : 0;
        $response["requested"] = $response["requested"] . "READ table [$prepare_state];"; // res;res;res
        array_push($response["data"], $res);
    }
    // free result set
    mysqli_free_result($query);

    if ($onlyRes) {
        return $res;
    } else {
        return $response;
    }
}

/**
 * Gets further information including 
 * user, feedback, tags, comments, comment_feedback 
 * about a video from the database.
 */
function getVideoInfo($connection, $response, $topic = "all", $bulk = false, $search_id = null)
{
    if (isset($_GET["id"])) {
        $id = intval(mysqli_real_escape_string($connection, strval($_GET["id"])));
        if ($id != 0) { // 0 on failure 1 on non empty arrays :/
            $response["info"]["fetch_id"] = $id; // logging

            if ($bulk) {
                if (!isset($response["data"]["user"]) || !is_array($response["data"]["user"])) {
                    $response["data"]["user"] = array();
                }

                if (!isset($response["data"]["feedback"]) || !is_array($response["data"]["feedback"])) {
                    $response["data"]["feedback"] = array();
                }

                if (!isset($response["data"]["tags"]) || !is_array($response["data"]["tags"])) {
                    $response["data"]["tags"] = array();
                }

                if (!isset($response["data"]["comments"]) || !is_array($response["data"]["comments"])) {
                    $response["data"]["comments"] = array();
                }

                if (!isset($response["data"]["comments_feedback"]) || !is_array($response["data"]["comments_feedback"])) {
                    $response["data"]["comments_feedback"] = array();
                }
            }

            if ($topic == "all" || $topic == "user") {
                $exists = checkIfIdExists($connection, "video_userID", $id);

                if ($exists) {
                    $query = mysqli_prepare($connection, 'SELECT * FROM VS_USER WHERE VS_USER_ID = (SELECT VS_USER_ID FROM VS_VIDEO WHERE VS_VIDEO_ID = ?)');
                    if ($bulk) {
                        $user = getMedium($connection, $response, $query, $id, true, "i", true);

                        $table_subscribers = mysqli_prepare($connection, 'SELECT USER_USERNAME, USER_PROFILEPICTURE FROM VS_USER WHERE VS_USER_ID IN (SELECT FOLLOWING_SUBSCRIBER FROM VS_USER_FOLLOWING WHERE FOLLOWING_SUBSCRIBED IN (SELECT VS_USER_ID FROM VS_VIDEO WHERE VS_VIDEO_ID = ?))');

                        $user[0]["subscribers"] = getMedium($connection, $response, $table_subscribers, $id, true, "i", true); // TEST ME (removed json_decode)

                        array_push($response["data"]["user"], $user);
                    } else {
                        $response["data"]["user"] = getMedium($connection, $response, $query, $id, true, "i", true);
                    }
                } else {
                    $response = errorOccurred($connection, $response, __LINE__, "user not found");
                }
            }

            if ($topic == "all" || $topic == "feedback") {
                $exists = checkIfIdExists($connection, "feedback_videoID", $id);

                if ($exists) {
                    $query = mysqli_prepare($connection, 'SELECT USER_USERNAME, USER_PROFILEPICTURE, VIDEO_FEEDBACK_ID, VIDEO_FEEDBACK_TYPE, VS_VIDEO_ID FROM VS_VIDEO_FEEDBACK, VS_USER WHERE VS_VIDEO_FEEDBACK.VS_USER_ID = VS_USER.VS_USER_ID AND VS_VIDEO_ID = ?');
                    if ($bulk) {
                        $feedback = getMedium($connection, $response, $query, $id, true, "i", true);

                        array_push($response["data"]["feedback"], $feedback);
                    } else {
                        $response["data"]["feedback"] = getMedium($connection, $response, $query, $id, true, "i", true);
                    }
                } else {
                    $response = errorOccurred($connection, $response, __LINE__, "feedback not found");
                }
            }

            if ($topic == "all" || $topic == "tags") {
                if (isset($_GET["medium_id"]) && ($search_id != "username" && $search_id != "tag" && $search_id != "title")) {
                    $hashtag = mysqli_real_escape_string($connection, strval($_GET["medium_id"]));
                    $hashtag_includes = '%' . $hashtag . '%';

                    array_push($response["info"]["fetch_params"], array("medium_id" => $hashtag)); // logging
                    $exists = checkIfIdExists($connection, "tags_name", $hashtag_includes, "s");

                    if ($exists) {
                        $query = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO_HASHTAG WHERE HASHTAG_NAME LIKE ?');
                        if ($bulk) {
                            $tags = getMedium($connection, $response, $query, $hashtag_includes, true, "s", true);
                            array_push($response["data"]["tags"], $tags);
                        } else {
                            $response["data"]["tags"] = getMedium($connection, $response, $query, $hashtag_includes, true, "s", true);
                        }
                    } else {
                        $response = errorOccurred($connection, $response, __LINE__, "hashtags not found");
                    }
                } else {
                    $exists = checkIfIdExists($connection, "tags_videoID", $id);

                    if ($exists) {
                        $query = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO_HASHTAG WHERE VS_VIDEO_ID = ?');
                        if ($bulk) {
                            $tags = getMedium($connection, $response, $query, $id, true, "i", true);
                            array_push($response["data"]["tags"], $tags);
                        } else {
                            $response["data"]["tags"] = getMedium($connection, $response, $query, $id, true, "i", true);
                        }
                    } else {
                        $response = errorOccurred($connection, $response, __LINE__, "hashtag not found");
                    }
                }
            }

            if ($topic == "all" || $topic == "comments") {
                $exists_1 = checkIfIdExists($connection, "video", $id);
                $exists_2 = checkIfIdExists($connection, "comments", $id);

                if ($exists_1 && $exists_2) {
                    $table_comment = mysqli_prepare($connection, 'SELECT vc.*, u.* FROM VS_VIDEO_COMMENT vc JOIN VS_USER u ON vc.VS_USER_ID = u.VS_USER_ID WHERE vc.VS_VIDEO_ID = ?');
                    if ($bulk) {
                        $comments = getMedium($connection, $response, $table_comment, $id, true, "i", true);
                        array_push($response["data"]["comments"], $comments);

                        for ($i = 0; $i < count($response["data"]["comments"]); $i++) {
                            foreach ($response["data"]["comments"][$i] as $comment) { // TEST ME (removed json_decode)
                                $video_id = $id;
                                $id = $comment["COMMENT_ID"];

                                $exists_3 = checkIfIdExists($connection, "comment_feedback", $video_id);
                                if ($exists_3) {
                                    $table_comment_feedback = "SELECT * FROM VS_COMMENT_FEEDBACK WHERE COMMENT_ID = $id";

                                    $comments_feedback = getMedium($connection, $response, $table_comment_feedback, null, false, "i", true);
                                    array_push($response["data"]["comments_feedback"], $comments_feedback);
                                } else {
                                    $response = errorOccurred($connection, $response, __LINE__, "comments feedback not found");
                                }
                            }
                        }
                    } else {
                        $response["data"]["comments"] = getMedium($connection, $response, $table_comment, $id, true, "i", true);

                        foreach ($response["data"]["comments"] as $comment) { // TEST ME (removed json_decode)
                            $video_id = $id;
                            $id = $comment["COMMENT_ID"];

                            $exists_3 = checkIfIdExists($connection, "comment_feedback", $video_id);
                            if ($exists_3) {
                                $table_comment_feedback = "SELECT * FROM VS_COMMENT_FEEDBACK WHERE COMMENT_ID = $id";
                                $response["data"]["comments_feedback"] = getMedium($connection, $response, $table_comment_feedback, null, false, "i", true);
                            } else {
                                $response = errorOccurred($connection, $response, __LINE__, "comments feedback not found");
                            }
                        }
                    }
                } else {
                    $response = errorOccurred($connection, $response, __LINE__, "comments not found");
                    $response = errorOccurred($connection, $response, __LINE__, "comments feedback not found");
                }
            }

            return $response;
        } else {
            errorOccurred($connection, $response, __LINE__, "id param invalid", true);
        }
    }
}

/**
 * Gets further information including 
 * socials, subscribed, liked, disliked, comments_liked, comments_disliked, videos, total likes, views and shares 
 * about a user from the database.
 */
function getUserInfo($connection, $response, $bind_var, $bind_type = "i")
{
    if ($bind_type == "s") {
        $table_socials = mysqli_prepare($connection, 'SELECT VS_USER_ID FROM VS_USER WHERE USER_USERNAME = ?');
        $user_id = getMedium($connection, $response, $table_socials, $bind_var, true, "s", true); // TEST ME (removed json_decode)
        $bind_var = $user_id[0]["VS_USER_ID"];

        // fetch more if fetched with username instead of id
        $table_socials = mysqli_prepare($connection, 'SELECT SOCIAL_PLATFORM, SOCIAL_URL FROM VS_USER_SOCIAL WHERE VS_USER_ID = ?');
        $response["data"]["socials"] = getMedium($connection, $response, $table_socials, $bind_var, true, "i", true);

        $table_subscribed = mysqli_prepare($connection, 'SELECT VS_USER_ID, USER_USERNAME, USER_PROFILEPICTURE FROM VS_USER WHERE VS_USER_ID IN (SELECT FOLLOWING_SUBSCRIBED FROM VS_USER_FOLLOWING WHERE FOLLOWING_SUBSCRIBER = ?)');
        $response["data"]["subscribed"] = getMedium($connection, $response, $table_subscribed, $bind_var, true, "i", true);

        $table_liked = mysqli_prepare($connection, 'SELECT VS_VIDEO_ID FROM VS_VIDEO_FEEDBACK WHERE VIDEO_FEEDBACK_TYPE LIKE "positive" AND VS_USER_ID = ?');
        $response["data"]["liked"] = getMedium($connection, $response, $table_liked, $bind_var, true, "i", true);

        $table_liked = mysqli_prepare($connection, 'SELECT VS_VIDEO_ID FROM VS_VIDEO_FEEDBACK WHERE VIDEO_FEEDBACK_TYPE LIKE "negative" AND VS_USER_ID = ?');
        $response["data"]["disliked"] = getMedium($connection, $response, $table_liked, $bind_var, true, "i", true);

        $table_liked = mysqli_prepare($connection, 'SELECT COMMENT_ID FROM VS_COMMENT_FEEDBACK WHERE COMMENT_FEEDBACK_TYPE LIKE "positive" AND VS_USER_ID = ?');
        $response["data"]["comments_liked"] = getMedium($connection, $response, $table_liked, $bind_var, true, "i", true);

        $table_liked = mysqli_prepare($connection, 'SELECT COMMENT_ID FROM VS_COMMENT_FEEDBACK WHERE COMMENT_FEEDBACK_TYPE LIKE "negative" AND VS_USER_ID = ?');
        $response["data"]["comments_disliked"] = getMedium($connection, $response, $table_liked, $bind_var, true, "i", true);

        // stats
        $table_videos = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO WHERE VS_USER_ID = ?');
        $response["data"]["stats"]["videos"] = getMedium($connection, $response, $table_videos, $bind_var, true, "i", true);

        $table_likes = mysqli_prepare($connection, 'SELECT COUNT(*) AS total FROM VS_VIDEO_FEEDBACK WHERE VIDEO_FEEDBACK_TYPE = "positive" AND VS_USER_ID = ?');
        $response["data"]["stats"]["likes"] = getMedium($connection, $response, $table_likes, $bind_var, true, "i", true);
        $table_views = mysqli_prepare($connection, 'SELECT SUM(VIDEO_VIEWS) AS total FROM VS_VIDEO WHERE VS_USER_ID = ?');
        $response["data"]["stats"]["views"] = getMedium($connection, $response, $table_views, $bind_var, true, "i", true);
        $table_shares = mysqli_prepare($connection, 'SELECT SUM(VIDEO_SHARES) AS total FROM VS_VIDEO WHERE VS_USER_ID = ?');
        $response["data"]["stats"]["shares"] = getMedium($connection, $response, $table_shares, $bind_var, true, "i", true);
    }

    $table_subscribers = mysqli_prepare($connection, 'SELECT VS_USER_ID, USER_USERNAME, USER_PROFILEPICTURE FROM VS_USER WHERE VS_USER_ID IN (SELECT FOLLOWING_SUBSCRIBER FROM VS_USER_FOLLOWING WHERE FOLLOWING_SUBSCRIBED = ?)');
    $response["data"]["subscribers"] = getMedium($connection, $response, $table_subscribers, $bind_var, true, "i", true);

    return $response;
}

/**
 * Exports data from database to ./database-backups/backup_[timestamp].sql.
 */
function export_database($connection)
{
    // read tables
    $tables = array();
    $result = mysqli_query($connection, 'SHOW TABLES');
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    // create dump file
    $backup_name = "backup_" . time() . ".sql";
    $backup_path = "./database-backups/" . $backup_name;
    $handle = fopen($backup_path, 'w');

    // export structure
    $i = 0;
    foreach ($tables as $table) {
        $result = mysqli_query($connection, 'SHOW CREATE TABLE ' . $table);
        $row = mysqli_fetch_row($result);

        if ($i != 0) {
            fwrite($handle, "\n\n" . $row[1] . ";\n\n");
        } else {
            fwrite($handle, $row[1] . ";\n\n");
        }

        $i++;

        // export
        $result = mysqli_query($connection, 'SELECT * FROM ' . $table);
        $num_fields = mysqli_num_fields($result);

        while ($row = mysqli_fetch_row($result)) {
            fwrite($handle, 'INSERT INTO ' . $table . ' VALUES(');

            for ($i = 0; $i < $num_fields; $i++) {
                if (isset($row[$i])) {
                    $value = $row[$i];
                } else {
                    $value = 'NULL';
                }

                if ($i == 0) {
                    fwrite($handle, '');
                } else {
                    fwrite($handle, ',');
                }

                fwrite($handle, "'" . mysqli_real_escape_string($connection, $value) . "'");
            }

            fwrite($handle, ");\n");
        }
    }

    fclose($handle);
}

/* ---------- */

// JWT - Authentication
// Using getenv() and putenv() is strongly discouraged due to the fact that these functions are not thread safe.
// https://github.com/vlucas/phpdotenv
$dotenv = Dotenv::createImmutable(__DIR__); // (.env content: PRIVATE_KEY="", PUBLIC_KEY="") || __DIR__ ,private_key.pem || public_key.pem
$dotenv->load(); // load .env file
$dotenv->required(['PRIVATE_KEY', 'PUBLIC_KEY', 'MYSQL_ROOT_PASSWORD'])->notEmpty(); // RS256

/* ---------- */

// INIT (MySQL-Database Cfg)
$host = 'host.docker.internal'; // database IP in docker container
$root = 'root';
$pass = $_ENV['MYSQL_ROOT_PASSWORD'];
$schema = 'vidslide';
$port = 3196; /* default: 3306 */

/* ---------- */

// RESPONSE
$response = array(
    "data" => array(), // response data object
    "info" => array(
        "database_connection_details" => array(
            "database_username" => $root // guest (GET access) or user with write privileges
        ),
        "fetch_method" => $_SERVER['REQUEST_METHOD'], // GET, POST, PUT, DELETE
        "fetch_object" => "", // medium
        "fetch_id" => "", // id of medium
        "fetch_params" => array() // medium_id of medium 
    ),
    "log" => array(),
    "token" => "", // unset, valid, invalid or hashed token string
    "response" => "", // Account and Authentication
    "requested" => "", // ACTIONS, READ, WRITE, MODIFY TABLE (res;res;res)
    "success" => false, // POST, PUT, DELETE res
    "error" => false // errors and warnings
);

// CONNECT
$connection = mysqli_connect($host, $root, $pass, "", $port); // $connection = mysqli_init(); + $connect_options = array(); + mysqli_real_connect(..., $connect_options (SSL and more));

/* ---------- */

// SETUP
if (!$connection) {
    errorOccurred($connection, $response, __LINE__, "connection error", true);
} else {
    $guest_user_username = "guest";
    $guest_user_password = "420GUEST69";
    if (isset($_GET["setup_db"]) && $_GET["setup_db"] === "true") { // ?setup_db=true SETUP DATABASE (first req?)
        include("db_setup.php");
    }

    mysqli_close($connection);

    // login as guest => READ ONLY
    $connection = mysqli_connect($host, $guest_user_username, $guest_user_password, $schema, $port);

    // export_database($connection);

    if (!$connection) {
        errorOccurred($connection, $response, __LINE__, "reconnection as guest user couldn't be established", true);
    } else {
        $response["info"]["database_connection_details"]["database_username"] = $guest_user_username;
        array_push($response["log"], date('H:i:s') . ": logged in as read only guest");
    }
}

/* ---------- */

try {
    // REQUEST METHODS | GET, POST, PUT, DELETE STATEMENTS

    // GET-Options: 
    // - medium=user [MEDIUM]
    //   - id=video [ID] // insufficient
    //     - medium_id=? [ID++] // creator of video
    //   - id=username [ID] 
    //     - medium_id=? [ID++] // username of user
    //   - id=? [ID] 
    // - medium=video [MEDIUM] // gets videos and video info
    //   - id=all [ID] // insufficient
    //     - medium_id=? [ID++] // all videos of user
    //   - id=title [ID]
    //     - medium_id=? [ID++] // all videos with title including text
    //   - id=tag [ID]
    //     - medium_id=? [ID++] // all videos with tag including text
    //   - id=username [ID]
    //     - medium_id=? [ID++] // all videos with username of the creator including text
    //   - id=random [ID]
    //   - id=? [ID] 
    // - medium=comments [MEDIUM]
    //   - id=? (video id) [ID] // get comments of video
    // - medium=tags [MEDIUM]
    //   - id=? (video id) [ID] // get tags of video
    // - medium=feedback [MEDIUM]
    //   - id=? (video id) [ID] // get feedback of video
    if ($_SERVER['REQUEST_METHOD'] === 'GET') { // no private data (password is hashed before writing it into the database)
        $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD']; // logging
        if (isset($_GET["medium"])) {
            $medium = $_GET["medium"];
            $response["info"]["fetch_object"] = $medium; // logging
            if ($medium == "user") { // ?medium=user [MEDIUM]
                if (isset($_GET["id"])) {
                    $id = mysqli_real_escape_string($connection, $_GET["id"]);

                    // logging
                    $response["info"]["fetch_id"] = $id;
                    $log = $medium . " with id=" . $id . " as " . $response["info"]["database_connection_details"]["database_username"] . " [" . $response["info"]["fetch_method"] . "]";
                    array_push($response["log"], date('H:i:s') . ": fetching " . $log);

                    if ($id == "video") { // ?medium=video&id=video [ID]
                        if (isset($_GET["medium_id"])) { // ?medium=user&id=video&medium_id=? [ID]
                            $videoID = intval(mysqli_real_escape_string($connection, $_GET["medium_id"]));
                            if ($videoID != 0) { // 0 on failure 1 on non empty arrays :/
                                array_push($response["info"]["fetch_params"], array("medium_id" => $videoID)); // logging
                                $exists = checkIfIdExists($connection, "video", $videoID);

                                if ($exists) {
                                    $table_user = mysqli_prepare($connection, 'SELECT * FROM VS_USER WHERE VS_USER_ID = (SELECT VS_USER_ID FROM VS_VIDEO WHERE VS_VIDEO_ID = ?)');
                                    $response = getMedium($connection, $response, $table_user, $videoID, true);
                                } else {
                                    $response = errorOccurred($connection, $response, __LINE__, "user not found");
                                }
                            } else {
                                errorOccurred($connection, $response, __LINE__, "id param invalid", true);
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "medium_id param missing", true);
                        }
                    } else if ($id == "username") {
                        if (isset($_GET["medium_id"])) { // ?medium=user&id=username&medium_id=? [ID]
                            $username = mysqli_real_escape_string($connection, $_GET["medium_id"]);

                            array_push($response["info"]["fetch_params"], array("medium_id" => $username)); // logging
                            $exists = checkIfIdExists($connection, "user_username", $username, "s");

                            if ($exists) {
                                $table_user = mysqli_prepare($connection, 'SELECT * FROM VS_USER WHERE USER_USERNAME = ?');
                                $response = getMedium($connection, $response, $table_user, $username, true, "s");

                                $response = getUserInfo($connection, $response, $username, "s");
                            } else {
                                $response = errorOccurred($connection, $response, __LINE__, "user not found");
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "medium_id param missing", true);
                        }
                    } else { // ?medium=user&id=? [ID]
                        $userID = intval($id);
                        if ($userID != 0) { // 0 on failure 1 on non empty arrays :/
                            $response["info"]["fetch_id"] = $userID; // logging
                            $exists = checkIfIdExists($connection, "user", $userID);

                            if ($exists) {
                                $table_user = mysqli_prepare($connection, 'SELECT * FROM VS_USER WHERE VS_USER_ID = ?');
                                $response = getMedium($connection, $response, $table_user, $userID, true);

                                $response = getUserInfo($connection, $response, $userID);
                            } else {
                                $response = errorOccurred($connection, $response, __LINE__, "user not found");
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "id param invalid", true);
                        }
                    }
                } else {
                    errorOccurred($connection, $response, __LINE__, "id param missing", true);
                }
            } else if ($medium == "video") { // ?medium=video [MEDIUM]
                if (isset($_GET["id"])) {
                    $id = mysqli_real_escape_string($connection, $_GET["id"]);
                    $response["info"]["fetch_id"] = $id; // logging

                    // logging
                    $response["info"]["fetch_id"] = $id;
                    $log = $medium . " with id=" . $id . " as " . $response["info"]["database_connection_details"]["database_username"] . " [" . $response["info"]["fetch_method"] . "]";
                    array_push($response["log"], date('H:i:s') . ": fetching " . $log);

                    if ($id == "all") {
                        if (isset($_GET["medium_id"])) { // ?medium=video&id=all&medium_id=1 [ID]
                            $userID = intval(mysqli_real_escape_string($connection, $_GET["medium_id"]));
                            if ($userID != 0) { // 0 on failure 1 on non empty arrays :/
                                array_push($response["info"]["fetch_params"], array("medium_id" => $userID)); // logging
                                $exists = checkIfIdExists($connection, "video", $userID);

                                if ($exists) {
                                    $table_video = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO WHERE VS_USER_ID = ? ORDER BY VS_VIDEO_ID DESC');
                                    $response = getMedium($connection, $response, $table_video, $userID, true);

                                    unset($_GET['medium_id']);
                                    $pulled_videos = $response["data"][0]; // TEST ME (removed json_decode)
                                    foreach ($pulled_videos as $video) { // get multiple video infos at once
                                        $_GET["id"] = $video["VS_VIDEO_ID"];
                                        $response = getVideoInfo($connection, $response, "all", true, $video["VS_VIDEO_ID"]);
                                    }
                                } else {
                                    $response = errorOccurred($connection, $response, __LINE__, "videos of user not found");
                                }
                            } else {
                                errorOccurred($connection, $response, __LINE__, "id param invalid", true);
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "medium_id param missing", true);
                        }
                    } else if ($id == "title") {
                        if (isset($_GET["medium_id"])) { // ?medium=video&id=title&medium_id=? [ID]
                            $title = mysqli_real_escape_string($connection, strval($_GET["medium_id"]));
                            $title_includes = "%$title%";

                            $exists = checkIfIdExists($connection, "video_title", $title_includes, "s");

                            if ($exists) {
                                $table_video = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO WHERE VIDEO_TITLE LIKE ?');
                                $response = getMedium($connection, $response, $table_video, $title_includes, true, "s");

                                $pulled_videos = $response["data"][0]; // TEST ME (removed json_decode)
                                foreach ($pulled_videos as $video) { // get multiple video infos at once
                                    $_GET["id"] = $video["VS_VIDEO_ID"];
                                    $response = getVideoInfo($connection, $response, "all", true, "title");
                                }
                            } else {
                                $response = errorOccurred($connection, $response, __LINE__, "video not found");
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "medium_id param missing", true);
                        }
                    } else if ($id == "tag") {
                        if (isset($_GET["medium_id"])) { // ?medium=video&id=tag&medium_id=? [ID]
                            $tag = mysqli_real_escape_string($connection, strval($_GET["medium_id"]));
                            $tag_includes = "%$tag%";

                            $exists = checkIfIdExists($connection, "video_tag", $tag_includes, "s");

                            if ($exists) {
                                $table_video = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO WHERE VS_VIDEO_ID IN (SELECT VS_VIDEO_ID FROM VS_VIDEO_HASHTAG WHERE HASHTAG_NAME LIKE ?)');
                                $response = getMedium($connection, $response, $table_video, $tag_includes, true, "s");

                                $pulled_videos = $response["data"][0]; // TEST ME (removed json_decode)
                                foreach ($pulled_videos as $video) { // get multiple video infos at once
                                    $_GET["id"] = $video["VS_VIDEO_ID"];
                                    $response = getVideoInfo($connection, $response, "all", true, "tag");
                                }
                            } else {
                                $response = errorOccurred($connection, $response, __LINE__, "video not found");
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "medium_id param missing", true);
                        }
                    } else if ($id == "username") {
                        if (isset($_GET["medium_id"])) { // ?medium=video&id=username&medium_id=? [ID]
                            $username = mysqli_real_escape_string($connection, strval($_GET["medium_id"]));
                            $username_includes = "%$username%";

                            $exists = checkIfIdExists($connection, "video_username", $username_includes, "s");

                            if ($exists) {
                                $table_video = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO WHERE VS_USER_ID IN (SELECT VS_USER_ID FROM VS_USER WHERE USER_USERNAME LIKE ?)');
                                $response = getMedium($connection, $response, $table_video, $username_includes, true, "s");

                                $pulled_videos = $response["data"][0]; // TEST ME (removed json_decode)
                                foreach ($pulled_videos as $video) { // get multiple video infos at once
                                    $_GET["id"] = $video["VS_VIDEO_ID"];
                                    $response = getVideoInfo($connection, $response, "all", true, "username");
                                }
                            } else {
                                $response = errorOccurred($connection, $response, __LINE__, "video not found");
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "medium_id param missing", true);
                        }
                    } else if ($id == "random") { // ?medium=video&id=random [ID]
                        $table_video = "SELECT * FROM VS_VIDEO ORDER BY RAND() LIMIT 1"; // inefficient
                        $response = getMedium($connection, $response, $table_video, $id, false);

                        $_GET["id"] = $response["data"][0][0]["VS_VIDEO_ID"]; // set id to video_id instead of random // TEST ME (removed json_decode)
                        $response = getVideoInfo($connection, $response);
                    } else { // ?medium=video&id=? [ID]
                        $videoID = intval($id);
                        if ($videoID != 0) { // 0 on failure 1 on non empty arrays :/
                            $response["info"]["fetch_id"] = $videoID; // logging
                            $exists = checkIfIdExists($connection, "video", $videoID);

                            if ($exists) {
                                $table_video = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO WHERE VS_VIDEO_ID = ?');
                                $response = getMedium($connection, $response, $table_video, $videoID, true);

                                $response = getVideoInfo($connection, $response);
                            } else {
                                $response = errorOccurred($connection, $response, __LINE__, "video not found");
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "id param invalid", true);
                        }
                    }
                } else {
                    errorOccurred($connection, $response, __LINE__, "id param missing", true);
                }
            } else if ($medium == "comments") { // ?medium=comments [MEDIUM]
                $response = getVideoInfo($connection, $response, "comments");
            } else if ($medium == "tags") { // ?medium=tags [MEDIUM]
                $response = getVideoInfo($connection, $response, "tags");
            } else if ($medium == "feedback") { // ?medium=feedback [MEDIUM]
                $response = getVideoInfo($connection, $response, "feedback");
            } else {
                errorOccurred($connection, $response, __LINE__, "medium param invalid", true);
            }
        } else {
            errorOccurred($connection, $response, __LINE__, "medium param missing", true);
        }
    }

    function get_HTTP_AUTHORIZATION_from_header($connection, $response)
    {
        $matches = "";

        $authentication_header = isset($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : $_POST["HTTP_AUTHORIZATION"]; // $_SERVER['HTTP_AUTHORIZATION'] is not set automatically
        if (!preg_match('/Bearer\s(\S+)/', $authentication_header, $matches)) {
            $response["response"] = "jwt not found in header!";
            $response["token"] = "invalid";
            errorOccurred($connection, $response, __LINE__, "jwt not found", true);
        }

        $jwt = $matches[1];
        $publicKey = $_ENV['PUBLIC_KEY'];

        return getJWT($connection, $response, $jwt, $publicKey);
    }

    // METHODS:
    // POST, PUT, DELETE
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD'];
        if (isset($_POST["action"]) && isset($_POST["medium"])) {
            $action = $_POST["action"];
            $medium = $_POST["medium"];

            if ($action === "POST") {
                // POST-Options:
                // - medium=auth [MEDIUM] // insufficient
                //   - username=?&password=? [ID] // => auth token if password for user is valid or account doesn't exist (will be created)
                // - medium=signout [MEDIUM]  
                // - medium=video [MEDIUM] 
                //   - HTTP_AUTHORIZATION=?&VIDEO_MEDIA=?&VIDEO_TITLE=?&VIDEO_DESCRIPTION=?
                // - medium=comment [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&COMMENT_MESSAGE=?&VS_VIDEO_ID=?(&COMMENT_PARENT_ID=?)
                // - medium=feedback [MEDIUM]
                //   - medium_id=? (type=comment|video) [ID]
                //     - HTTP_AUTHORIZATION=?&FEEDBACK_TYPE=?&VS_VIDEO_ID=?||VS_COMMENT_ID=?
                // - medium=follow [MEDIUM]
                //  - HTTP_AUTHORIZATION=?&FOLLOWING_SUBSCRIBED=? 

                // authentication
                // https://github.com/firebase/php-jwt
                if ($medium == "auth") { // signup && signin
                    // authentication
                    // https://github.com/firebase/php-jwt
                    $privateKey = $_ENV['PRIVATE_KEY'];
                    $issuedAt   = new DateTimeImmutable();
                    $expire     = $issuedAt->modify('+7 days')->getTimestamp(); // expires after 7 days
                    $serverName = "svelte-kit-vid-slide.vercel.app";

                    if (isset($_POST["username"]) && isset($_POST["password"])) {
                        $username = mysqli_real_escape_string($connection, $_POST["username"]);
                        $password = mysqli_real_escape_string($connection, $_POST["password"]);
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        mysqli_close($connection);
                        $connection = mysqli_connect($host, $root, $pass, "", $port);
                        mysqli_select_db($connection, $schema);

                        if (!$connection) {
                            errorOccurred($connection, $response, __LINE__, "connection error", true);
                        } else {
                            $exists = checkIfIdExists($connection, "user_username", $username, "s");

                            if ($exists) {
                                // send user data
                                $table_user = mysqli_prepare($connection, 'SELECT * FROM VS_USER WHERE USER_USERNAME = ?');
                                $response = getMedium($connection, $response, $table_user, $username, true, "s");
                                $response = getUserInfo($connection, $response, $username, "s");

                                $response_data = $response["data"][0][0]; // TEST ME (removed json_decode)
                                $password_from_database = $response_data["USER_PASSWORD"];
                                $user_id = $response_data["VS_USER_ID"];

                                $payload = [
                                    'iat'  => $issuedAt->getTimestamp(),         // time when the token was generated
                                    'nbf'  => $issuedAt->getTimestamp(),         // not before
                                    'iss'  => $serverName,                       // issuer
                                    'exp'  => $expire,                           // expire
                                    "user_id" => $user_id
                                ];

                                if (password_verify($password, $password_from_database)) {
                                    $response["response"] = "accountExisted";
                                    $response["info"]["database_connection_details"]["database_username"] = $username;
                                    $response["token"] = sendJWT($payload, $privateKey);
                                } else {
                                    $response = errorOccurred($connection, $response, __LINE__, "invalid password");
                                }
                            } else {
                                $table_user_insert = mysqli_prepare($connection, "INSERT INTO VS_USER (USER_USERNAME, USER_PASSWORD) VALUES (?, ?)");
                                mysqli_stmt_bind_param($table_user_insert, 'ss', $username, $hashed_password);
                                mysqli_stmt_execute($table_user_insert);

                                if (mysqli_affected_rows($connection) > 0) {
                                    array_push($response["log"], date('H:i:s') . ": created new user: " . $username);

                                    mysqli_stmt_close($table_user_insert);

                                    $response["response"] = "accountDidNotExist";
                                    $response["info"]["database_connection_details"]["database_username"] = $username;

                                    // send user data
                                    $table_user = mysqli_prepare($connection, 'SELECT * FROM VS_USER WHERE USER_USERNAME = ?');
                                    $response = getMedium($connection, $response, $table_user, $username, true, "s");
                                    $response = getUserInfo($connection, $response, $username, "s");

                                    $response_data = $response["data"][0][0]; // TEST ME (removed json_decode)
                                    $user_id = $response_data["VS_USER_ID"];

                                    $payload = [
                                        'iat'  => $issuedAt->getTimestamp(),         // time when the token was generated
                                        'nbf'  => $issuedAt->getTimestamp(),         // not before
                                        'iss'  => $serverName,                       // issuer
                                        'exp'  => $expire,                           // expire
                                        "user_id" => $user_id
                                    ];

                                    $response["token"] = sendJWT($payload, $privateKey);
                                } else {
                                    errorOccurred($connection, $response, __LINE__, "user insert failed", true);
                                }
                            }
                        }
                    } else {
                        errorOccurred($connection, $response, __LINE__, "Username or password is missing!", true);
                    }
                } else if ($medium == "signout") {
                    $response["token"] = 'unset';
                } else if ($medium == "video") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        if (isset($_FILES["VIDEO_MEDIA"])) {
                            $video_media = $_FILES["VIDEO_MEDIA"];
                            $video_media_name = pathinfo(mysqli_real_escape_string($connection, $_FILES["VIDEO_MEDIA"]["name"]), PATHINFO_FILENAME);
                            $video_media_tmp_name = mysqli_real_escape_string($connection, $_FILES["VIDEO_MEDIA"]["tmp_name"]); // temp name on server
                            $video_media_size = mysqli_real_escape_string($connection, strval($_FILES["VIDEO_MEDIA"]["size"])); // in bytes
                            $video_media_type = mysqli_real_escape_string($connection, $_FILES["VIDEO_MEDIA"]["type"]); // MIME-Typ

                            $supported_video_formats = array(
                                "AVI",
                                "MP4",
                                "MKV",
                                "MOV",
                                "WMV",
                                "FLV",
                                "MPEG",
                                "WebM",
                                "3GP"
                            );

                            $video_title = isset($_POST["VIDEO_TITLE"]) ? mysqli_real_escape_string($connection, $_POST["VIDEO_TITLE"]) : "";
                            $video_description = isset($_POST["VIDEO_DESCRIPTION"]) ? mysqli_real_escape_string($connection, $_POST["VIDEO_DESCRIPTION"]) : "";

                            if ($_FILES["VIDEO_MEDIA"]["error"] == 0 && in_array(strtolower($video_media_type), array_map('strtolower', $supported_video_formats))) {
                                $media_video_path = "./media/video/uploaded/";

                                if (!is_dir($media_video_path)) {
                                    mkdir($media_video_path);
                                }

                                $media_video_filename = preg_replace('/[^A-Za-z0-9\-\_]/', '0', $video_title); // replaces every special char with 0
                                $media_video_filename = preg_replace('/\s+/', '_', $media_video_filename); // replaces every SPACE with _

                                $files = scandir($media_video_path);
                                $files_in_folder = array_slice($files, 2); // removes "." and ".."
                                $files_count = count($files_in_folder) + 1; // count files

                                $uploaded_video_name = $media_video_filename . "_$files_count" . $video_media_type;
                                $uploaded_video_path = $media_video_path . $uploaded_video_name;
                                move_uploaded_file($video_media_tmp_name, $uploaded_video_path);

                                // Video Processing
                                $media_video_compressed_path = "./media/video/compressed/";

                                $ffmpegConfig = array(
                                    'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
                                    'ffprobe.binaries' => '/usr/local/bin/ffprobe',
                                );

                                $ffmpeg = FFMpeg::create($ffmpegConfig);
                                $format = new X264();
                                $dimensions = new Dimension(1024, 576);

                                try {
                                    /** @var \FFMpeg\Media\Video */
                                    $video = $ffmpeg->open($uploaded_video_path);
                                } catch (InvalidArgumentException $e) {
                                    errorOccurred($connection, $response, __LINE__, $e->getMessage(), true);
                                }

                                $video->filters()->resize($dimensions);

                                try {
                                    $video->save($format, $media_video_compressed_path . $uploaded_video_name);
                                } catch (RuntimeException $e) {
                                    errorOccurred($connection, $response, __LINE__, $e->getMessage(), true);
                                }

                                mysqli_close($connection);
                                $connection = mysqli_connect($host, $root, $pass, "", $port);
                                mysqli_select_db($connection, $schema);

                                if (!$connection) {
                                    errorOccurred($connection, $response, __LINE__, "connection error", true);
                                } else {
                                    $exists = checkIfIdExists($connection, "user", $user_id, "s"); // TODO

                                    if ($exists) {
                                        $table_video_insert = mysqli_prepare($connection, "INSERT INTO VS_VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, VIDEO_LOCATION, VIDEO_SIZE, VS_USER_ID) VALUES ('?', '?', '?', '?', ?)");
                                        mysqli_stmt_bind_param($table_video_insert, 'ssssi', $video_title, $video_description, $uploaded_video_name, $video_media_size, $user_id);
                                        mysqli_stmt_execute($table_video_insert);

                                        if (mysqli_affected_rows($connection) > 0) {
                                            array_push($response["log"], date('H:i:s') . ": inserted new video, VS_USER_ID:" . $user_id);
                                            mysqli_stmt_close($table_video_insert);
                                        } else {
                                            errorOccurred($connection, $response, __LINE__, "video insert failed", true);
                                        }
                                    } else {
                                        $response = errorOccurred($connection, $response, __LINE__, "user doesn't exist");
                                    }
                                }
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "video missing", true);
                        }

                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "comment") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        if (isset($_POST["COMMENT_MESSAGE"]) && isset($_POST["VS_VIDEO_ID"])) { // TODO: COMMENT_PARENT_ID
                            $comment_message = $_POST["COMMENT_MESSAGE"];
                            $video_id = $_POST["VS_VIDEO_ID"];

                            mysqli_close($connection);
                            $connection = mysqli_connect($host, $root, $pass, $schema, $port);

                            if (!$connection) {
                                errorOccurred($connection, $response, __LINE__, "connection error", true);
                            } else {
                                $exists = checkIfIdExists($connection, "video", $video_id, "i");

                                if ($exists) {
                                    $table_comment_insert = mysqli_prepare($connection, "INSERT INTO VS_VIDEO_COMMENT (COMMENT_MESSAGE, VS_VIDEO_ID, VS_USER_ID) VALUES (?, ?, ?)");
                                    mysqli_stmt_bind_param($table_comment_insert, 'sii', $comment_message, $video_id, $user_id);
                                    mysqli_stmt_execute($table_comment_insert);

                                    if (mysqli_affected_rows($connection) > 0) {
                                        array_push($response["log"], date('H:i:s') . ": inserted new comment, VS_USER_ID:" . $user_id);
                                        mysqli_stmt_close($table_comment_insert);

                                        $response["success"] = true;
                                    } else {
                                        errorOccurred($connection, $response, __LINE__, "comment insert failed", true);
                                    }
                                } else {
                                    $response = errorOccurred($connection, $response, __LINE__, "video doesn't exist");
                                }
                            }
                        }

                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "feedback") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        if (isset($_POST["medium_id"])) {
                            $type = mysqli_real_escape_string($connection, strval($_POST["medium_id"]));

                            if (isset($_POST["FEEDBACK_TYPE"])) {
                                $feedback_type = $_POST["FEEDBACK_TYPE"];

                                if ($type === "comment") {
                                    if (isset($_POST["COMMENT_ID"])) {
                                        $comment_id = $_POST["COMMENT_ID"];

                                        mysqli_close($connection);
                                        $connection = mysqli_connect($host, $root, $pass, $schema, $port);

                                        if (!$connection) {
                                            errorOccurred($connection, $response, __LINE__, "connection error", true);
                                        } else {
                                            $exists = checkIfIdExists($connection, "comments", $comment_id, "i");

                                            if ($exists) {
                                                $doesntHurtConstraint = doesntHurtConstraint($connection, "VS_COMMENT_FEEDBACK", "ii", "object", $comment_id, $user_id);

                                                if (empty($doesntHurtConstraint[0])) { // create new comment feedback
                                                    $table_comment_feedback_insert = mysqli_prepare($connection, "INSERT INTO VS_COMMENT_FEEDBACK (COMMENT_FEEDBACK_TYPE, COMMENT_ID, VS_USER_ID) VALUES (?, ?, ?)");
                                                    mysqli_stmt_bind_param($table_comment_feedback_insert, 'sii', $feedback_type, $comment_id, $user_id);
                                                    mysqli_stmt_execute($table_comment_feedback_insert);

                                                    if (mysqli_affected_rows($connection) > 0) {
                                                        array_push($response["log"], date('H:i:s') . ": inserted new comment feedback, VS_USER_ID:" . $user_id);
                                                        mysqli_stmt_close($table_comment_feedback_insert);

                                                        $response["success"] = true;
                                                    } else {
                                                        errorOccurred($connection, $response, __LINE__, "comment feedback insert failed", true);
                                                    }
                                                } else if ($doesntHurtConstraint[0]["COMMENT_FEEDBACK_TYPE"] != $feedback_type) { // update comment feedback
                                                    $table_comment_feedback_update = mysqli_prepare($connection, "UPDATE VS_COMMENT_FEEDBACK SET COMMENT_FEEDBACK_TYPE = ? WHERE COMMENT_ID = ?");
                                                    mysqli_stmt_bind_param($table_comment_feedback_update, 'si', $feedback_type, $comment_id);
                                                    mysqli_stmt_execute($table_comment_feedback_update);

                                                    if (mysqli_affected_rows($connection) > 0) {
                                                        array_push($response["log"], date('H:i:s') . ": updated comment feedback, VS_USER_ID:" . $user_id);
                                                        mysqli_stmt_close($table_comment_feedback_update);

                                                        $response["success"] = true;
                                                    } else {
                                                        errorOccurred($connection, $response, __LINE__, "comment feedback update failed", true);
                                                    }
                                                } else { // delete feedback
                                                    $table_comment_feedback_delete = mysqli_prepare($connection, "DELETE FROM VS_COMMENT_FEEDBACK WHERE COMMENT_ID = ?");
                                                    mysqli_stmt_bind_param($table_comment_feedback_delete, 'i', $comment_id);
                                                    mysqli_stmt_execute($table_comment_feedback_delete);

                                                    if (mysqli_affected_rows($connection) > 0) {
                                                        array_push($response["log"], date('H:i:s') . ": deleted comment feedback, VS_USER_ID:" . $user_id);
                                                        mysqli_stmt_close($table_comment_feedback_delete);

                                                        $response["success"] = null;
                                                    } else {
                                                        errorOccurred($connection, $response, __LINE__, "comment feedback deletion failed", true);
                                                    }
                                                }
                                            } else {
                                                $response = errorOccurred($connection, $response, __LINE__, "comment doesn't exist");
                                            }
                                        }
                                    }
                                } else if ($type === "video") {
                                    if (isset($_POST["VS_VIDEO_ID"])) {
                                        $video_id = $_POST["VS_VIDEO_ID"];

                                        mysqli_close($connection);
                                        $connection = mysqli_connect($host, $root, $pass, $schema, $port);

                                        if (!$connection) {
                                            errorOccurred($connection, $response, __LINE__, "connection error", true);
                                        } else {
                                            $exists = checkIfIdExists($connection, "video", $video_id, "i");

                                            if ($exists) {
                                                $doesntHurtConstraint = doesntHurtConstraint($connection, "VS_VIDEO_FEEDBACK", "ii", "object", $video_id, $user_id);

                                                if (empty($doesntHurtConstraint[0])) { // create new video feedback
                                                    $table_video_feedback_insert = mysqli_prepare($connection, "INSERT INTO VS_VIDEO_FEEDBACK (VIDEO_FEEDBACK_TYPE, VS_VIDEO_ID, VS_USER_ID) VALUES (?, ?, ?)");
                                                    mysqli_stmt_bind_param($table_video_feedback_insert, 'sii', $feedback_type, $video_id, $user_id);
                                                    mysqli_stmt_execute($table_video_feedback_insert);

                                                    if (mysqli_affected_rows($connection) > 0) {
                                                        array_push($response["log"], date('H:i:s') . ": inserted new video feedback, VS_USER_ID:" . $user_id);
                                                        mysqli_stmt_close($table_video_feedback_insert);

                                                        $response["success"] = true;
                                                    } else {
                                                        errorOccurred($connection, $response, __LINE__, "video feedback insert failed", true);
                                                    }
                                                } else if ($doesntHurtConstraint[0]["VIDEO_FEEDBACK_TYPE"] != $feedback_type) { // update video feedback
                                                    $table_video_feedback_update = mysqli_prepare($connection, "UPDATE VS_VIDEO_FEEDBACK SET VIDEO_FEEDBACK_TYPE = ? WHERE VS_VIDEO_ID = ?");
                                                    mysqli_stmt_bind_param($table_video_feedback_update, 'si', $feedback_type, $video_id);
                                                    mysqli_stmt_execute($table_video_feedback_update);

                                                    if (mysqli_affected_rows($connection) > 0) {
                                                        array_push($response["log"], date('H:i:s') . ": updated video feedback, VS_USER_ID:" . $user_id);
                                                        mysqli_stmt_close($table_video_feedback_update);

                                                        $response["success"] = true;
                                                    } else {
                                                        errorOccurred($connection, $response, __LINE__, "video feedback update failed", true);
                                                    }
                                                } else { // delete feedback
                                                    $table_video_feedback_delete = mysqli_prepare($connection, "DELETE FROM VS_VIDEO_FEEDBACK WHERE VS_VIDEO_ID = ?");
                                                    mysqli_stmt_bind_param($table_video_feedback_delete, 'i', $video_id);
                                                    mysqli_stmt_execute($table_video_feedback_delete);

                                                    if (mysqli_affected_rows($connection) > 0) {
                                                        array_push($response["log"], date('H:i:s') . ": deleted video feedback, VS_USER_ID:" . $user_id);
                                                        mysqli_stmt_close($table_video_feedback_delete);

                                                        $response["success"] = null;
                                                    } else {
                                                        errorOccurred($connection, $response, __LINE__, "video video deletion failed", true);
                                                    }
                                                }
                                            } else {
                                                $response = errorOccurred($connection, $response, __LINE__, "video doesn't exist");
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "medium_id param missing", true);
                        }

                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "follow") { // as FOLLOWING_SUBSCRIBER
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        if (isset($_POST["FOLLOWING_SUBSCRIBED"])) {
                            $subscribed_user = $_POST["FOLLOWING_SUBSCRIBED"];

                            mysqli_close($connection);
                            $connection = mysqli_connect($host, $root, $pass, $schema, $port);

                            if (!$connection) {
                                errorOccurred($connection, $response, __LINE__, "connection error", true);
                            } else {
                                $exists = checkIfIdExists($connection, "user", $subscribed_user, "i");

                                if ($exists) {
                                    $doesntHurtConstraint = doesntHurtConstraint($connection, "VS_USER_FOLLOWING", "ii", "boolean", $user_id, $subscribed_user);

                                    if ($doesntHurtConstraint) {
                                        $table_follow_insert = mysqli_prepare($connection, "INSERT INTO VS_USER_FOLLOWING (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED) VALUES (?, ?)");
                                        mysqli_stmt_bind_param($table_follow_insert, 'ii', $user_id, $subscribed_user);
                                        mysqli_stmt_execute($table_follow_insert);

                                        if (mysqli_affected_rows($connection) > 0) {
                                            array_push($response["log"], date('H:i:s') . ": inserted new follow, VS_USER_ID:" . $user_id);
                                            mysqli_stmt_close($table_follow_insert);

                                            $response["success"] = true;
                                        } else {
                                            errorOccurred($connection, $response, __LINE__, "follow insert failed", true);
                                        }
                                    } else {
                                        $table_follow_delete = mysqli_prepare($connection, "DELETE FROM VS_USER_FOLLOWING WHERE FOLLOWING_SUBSCRIBER = ? AND FOLLOWING_SUBSCRIBED = ?");
                                        mysqli_stmt_bind_param($table_follow_delete, 'ii', $user_id, $subscribed_user);
                                        mysqli_stmt_execute($table_follow_delete);

                                        if (mysqli_affected_rows($connection) > 0) {
                                            array_push($response["log"], date('H:i:s') . ": deleted follow, VS_USER_ID:" . $user_id);
                                            mysqli_stmt_close($table_follow_delete);

                                            $response["success"] = null;
                                        } else {
                                            errorOccurred($connection, $response, __LINE__, "follow deletion failed", true);
                                        }
                                    }
                                } else {
                                    $response = errorOccurred($connection, $response, __LINE__, "user doesn't exist");
                                }
                            }
                        }

                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                }
            } else if ($action === "PUT") {
                // PUT-Options:
                // - medium=profile_username [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&USER_USERNAME=?
                // - medium=profile_password [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&USER_PASSWORD=?
                // - medium=profile_description [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&USER_PROFILEDESCRIPTION=?
                // - medium=profile_socials [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&VS_USER_SOCIAL=? (socials object)
                // - medium=profile_picture [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&USER_PROFILEPICTURE=?
                // - medium=video_post_title [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VIDEO_TITLE=?
                // - medium=video_post_description [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VIDEO_DESCRIPTION=?
                // - medium=video_post_hashtags [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VS_VIDEO_HASHTAG=? (hashtag object)
                // - medium=comment_post_text [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&COMMENT_ID=?&COMMENT_MESSAGE=?
                if ($medium == "profile_username") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "profile_password") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "profile_description") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "profile_socials") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "profile_picture") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        if (isset($_FILES["IMAGE_MEDIA"])) {
                            $supported_image_formats = array(
                                'jpg', 'jpeg', 'jpe', 'jif', 'jfif', 'jfi',
                                'png',
                                'bmp', "dib",
                                'webp',
                                'gif'
                            );
                            $width = 50;
                            $height = 50;

                            $media_image_path = "./media/image/uploaded/";
                            $type = $_FILES["IMAGE_MEDIA"]["type"];
                            if ($_FILES["VIDEO_MEDIA"]["error"] == 0 && in_array(strtolower($video_media_type), array_map('strtolower', $supported_image_formats))) {
                                switch ($type) {
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'jpe':
                                    case 'jif':
                                    case 'jfif':
                                    case 'jfi':
                                        $image = imagecreatefromjpeg($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                        imagejpeg(imagescale($image, $width, $height), $media_image_path);
                                        // TODO: Database Save
                                        break;
                                    case 'png':
                                        $image = imagecreatefrompng($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                        imagepng(imagescale($image, $width, $height), $media_image_path);
                                        // TODO: Database Save
                                        break;
                                    case 'gif':
                                        $image = imagecreatefromgif($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                        imagegif(imagescale($image, $width, $height), $media_image_path);
                                        // TODO: Database Save
                                        break;
                                    case 'bmp':
                                    case 'dib':
                                        $image = imagecreatefrombmp($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                        imagebmp(imagescale($image, $width, $height), $media_image_path);
                                        // TODO: Database Save
                                        break;
                                    case 'webp':
                                        $image = imagecreatefromwebp($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                        imagewebp(imagescale($image, $width, $height), $media_image_path);
                                        // TODO: Database Save
                                        break;
                                    default:
                                        break;
                                }
                            } else {
                                errorOccurred($connection, $response, __LINE__, "image not supported", true);
                            }
                        }

                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "video_post_title") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "video_post_description") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "video_post_hashtags") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "comment_post_text") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                }
            } else if ($action === "DELETE") {
                // DELETE-Options:
                // - medium=account [MEDIUM]
                //   - HTTP_AUTHORIZATION=?
                // - medium=video [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?
                // - medium=comment [MEDIUM]
                //   - HTTP_AUTHORIZATION=?&COMMENT_ID=?

                if ($medium == "account") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "video") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                } else if ($medium == "comment") {
                    $jwt_received = get_HTTP_AUTHORIZATION_from_header($connection, $response);

                    if ($jwt_received["user_id"]) {
                        $user_id = $jwt_received["user_id"];

                        // TODO: Database Save and response
                        $response["token"] = "valid";
                    } else {
                        $response["token"] = "invalid";
                        errorOccurred($connection, $response, __LINE__, "invalid jwt", true);
                    }
                }
            }
        }
    }
} catch (Exception $e) {
    errorOccurred($connection, $response, __LINE__, "something went wrong: " . $e->getMessage(), true);
}

/* ---------- */

// CLEAN UP
unset($response["log"]); // comment for debugging

$response["data"] = array_filter($response["data"], function ($index) {
    return !empty($index);
});

// Disconnect
if (mysqli_close($connection)) {
    echo json_encode($response);
}

/* ---------- */

// SCRIPT END
exit();
