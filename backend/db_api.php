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
// on error
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
// sends token to client
function sendJWT($payload, $privateKey)
{
    $jwt = JWT::encode($payload, $privateKey, 'RS256');
    return $jwt;
}

// gets and validates token from client
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

// gets data from database
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
        $res = json_encode($rows);
        mysqli_stmt_close($table);
    } else {
        // execute query
        $query = mysqli_query($connection, $table); // SELECT, SHOW, DESCRIBE or EXPLAIN returns mysqli_result object // mysqli_real_query() => doesn't wait for response // mysqli_reap_async_query() => async
        // fetch all
        $rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $res = json_encode($rows);
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

function getVideoInfo($connection, $response, $topic = "all", $bulk = false)
{
    if (isset($_GET["id"])) {
        $escaped_id = mysqli_real_escape_string($connection, strval($_GET["id"]));
        $id = $escaped_id != "all" ? intval($escaped_id) : $escaped_id;
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
                    $query = mysqli_prepare($connection, 'SELECT * FROM VS_VIDEO_FEEDBACK WHERE VS_VIDEO_ID = ?');
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
                if (isset($_GET["medium_id"])) {
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
                            foreach (json_decode($response["data"]["comments"][$i], true) as $comment) {
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

                        foreach (json_decode($response["data"]["comments"], true) as $comment) {
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

function getUserInfo($connection, $response, $bind_var, $bind_type = "i")
{
    if ($bind_type == "s") {
        $table_socials = mysqli_prepare($connection, 'SELECT VS_USER_ID FROM VS_USER WHERE USER_USERNAME = ?');
        $user_id = json_decode(getMedium($connection, $response, $table_socials, $bind_var, true, "s", true), true);
        $bind_var = $user_id[0]["VS_USER_ID"];

        // fetch more if fetched with username instead of id
        $table_socials = mysqli_prepare($connection, 'SELECT SOCIAL_PLATFORM, SOCIAL_URL FROM VS_USER_SOCIAL WHERE VS_USER_ID = ?');
        $response["data"]["socials"] = getMedium($connection, $response, $table_socials, $bind_var, true, "i", true);

        $table_subscribed = mysqli_prepare($connection, 'SELECT USER_USERNAME, USER_PROFILEPICTURE FROM VS_USER WHERE VS_USER_ID IN (SELECT FOLLOWING_SUBSCRIBED FROM VS_USER_FOLLOWING WHERE FOLLOWING_SUBSCRIBER = ?)');
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

    $table_subscribers = mysqli_prepare($connection, 'SELECT USER_USERNAME, USER_PROFILEPICTURE FROM VS_USER WHERE VS_USER_ID IN (SELECT FOLLOWING_SUBSCRIBER FROM VS_USER_FOLLOWING WHERE FOLLOWING_SUBSCRIBED = ?)');
    $response["data"]["subscribers"] = getMedium($connection, $response, $table_subscribers, $bind_var, true, "i", true);

    return $response;
}

// exports data from database
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
    "data" => array(),
    "info" => array(
        "database_connection_details" => array(
            "database_username" => $root
        ),
        "fetch_method" => $_SERVER['REQUEST_METHOD'],
        "fetch_object" => "",
        "fetch_id" => "",
        "fetch_params" => array()
    ),
    "log" => array(),
    "token" => "",
    "response" => "",
    "requested" => "",
    "error" => false
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
    if (!$connection) {
        errorOccurred($connection, $response, __LINE__, "reconnection as guest user couldn't be astablished", true);
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
    //   - id=video [ID] // unsuffishient
    //     - medium_id=? [ID++] // creator of video
    //   - id=username [ID] 
    //     - medium_id=? [ID++] // username of user
    //   - id=? [ID] 
    // - medium=video [MEDIUM] // gets videos and video info
    //   - id=all [ID] // unsuffishient
    //     - medium_id=? [ID++] // all videos of user
    //   - id=title [ID]
    //     - medium_id=? [ID++] // all videos with title including text
    //   - id=tag [ID]
    //     - medium_id=? [ID++] // all videos with tag including text
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
                    $response["response"] = "fetched " . $log;

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
                    $response["response"] = "fetched " . $log;

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
                                    $pulled_videos = json_decode($response["data"][0], true);
                                    foreach ($pulled_videos as $video) { // get multiple video infos at once
                                        $_GET["id"] = $video["VS_VIDEO_ID"];
                                        $response = getVideoInfo($connection, $response, "all", true);
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

                                $pulled_videos = json_decode($response["data"][0], true);
                                foreach ($pulled_videos as $video) { // get multiple video infos at once
                                    $_GET["id"] = $video["VS_VIDEO_ID"];
                                    $response = getVideoInfo($connection, $response, "all", true);
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

                                $pulled_videos = json_decode($response["data"][0], true);
                                foreach ($pulled_videos as $video) { // get multiple video infos at once
                                    $_GET["id"] = $video["VS_VIDEO_ID"];
                                    $response = getVideoInfo($connection, $response, "all", true);
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

                        $_GET["id"] = json_decode($response["data"][0], true)[0]["VS_VIDEO_ID"]; // set id to video_id instead of random
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

    // TODO: Manage Authentication

    // POST-Options:
    // - action=auth [MEDIUM] // unsuffishient
    //   - username=?&password=? [ID] // => auth token if password for user is valid or account doesnt exist (will be created)
    // - action=signout [MEDIUM]  
    // TODO
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD'];
        if (isset($_POST["action"])) {
            $action = $_POST["action"];

            // authentication
            // https://github.com/firebase/php-jwt
            if ($action == "auth") { // signup && signin
                // authentication
                // https://github.com/firebase/php-jwt
                $privateKey = $_ENV['PRIVATE_KEY'];
                $issuedAt   = new DateTimeImmutable();
                $expire     = $issuedAt->modify('+60 seconds')->getTimestamp();
                $serverName = "svelte-kit-vid-slide.vercel.app";

                if (isset($_POST["username"]) && isset($_POST["password"])) {
                    $username = mysqli_real_escape_string($connection, $_POST["username"]);
                    $password = mysqli_real_escape_string($connection, $_POST["password"]);
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $payload = [
                        'iat'  => $issuedAt->getTimestamp(),         // time when the token was generated
                        'nbf'  => $issuedAt->getTimestamp(),         // not before
                        'iss'  => $serverName,                       // issuer
                        'exp'  => $expire,                           // expire
                        "username" => $username
                    ];

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

                            $password_from_database = json_decode($response["data"][0], true)[0]["USER_PASSWORD"];

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
                                $response["token"] = sendJWT($payload, $privateKey);

                                mysqli_stmt_close($table_user_insert);

                                $response["response"] = "accountDidNotExist";
                                $response["info"]["database_connection_details"]["database_username"] = $username;

                                // send user data
                                $table_user = mysqli_prepare($connection, 'SELECT * FROM VS_USER WHERE USER_USERNAME = ?');
                                $response = getMedium($connection, $response, $table_user, $username, true, "s");
                                $response = getUserInfo($connection, $response, $username, "s");
                            } else {
                                errorOccurred($connection, $response, __LINE__, "user insert failed", true);
                            }
                        }
                    }
                }
            } else if ($action == "signout") {
                $response["token"] = 'unset';
            } else if ($action == "video") {
                if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
                    $response["response"] = 'Token not found in request';
                    $response["token"] = "invalid";
                    errorOccurred($connection, $response, __LINE__, "jwt not found", true);
                }

                $jwt = $matches[1];
                $publicKey = $_ENV['PUBLIC_KEY'];
                $jwt_received = getJWT($connection, $response, $jwt, $publicKey);

                if ($jwt_received["username"]) {
                    $username = $jwt_received["username"];

                    if (isset($_FILES["VIDEO_MEDIA"])) {
                        $video_media = $_FILES["VIDEO_MEDIA"];
                        $video_media_name = pathinfo(mysqli_real_escape_string($connection, $_FILES["VIDEO_MEDIA"]["name"]), PATHINFO_FILENAME);
                        $video_media_tmp_name = mysqli_real_escape_string($connection, $_FILES["VIDEO_MEDIA"]["tmp_name"]); // temp name on server
                        $video_media_size = mysqli_real_escape_string($connection, $_FILES["VIDEO_MEDIA"]["size"]); // in bytes
                        $video_media_type = mysqli_real_escape_string($connection, $_FILES["VIDEO_MEDIA"]["type"]); // MIME-Typ

                        $supportet_video_formats = array(
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

                        if ($_FILES["VIDEO_MEDIA"]["error"] == 0 && in_array(strtolower($video_media_type), array_map('strtolower', $supportet_video_formats))) {
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
                                $exists = checkIfIdExists($connection, "user_username", $username, "s");

                                if ($exists) {
                                    $user_id = json_decode(getUserInfo($connection, $response, $username, "s")["data"][0], true)[0]["VS_USER_ID"];

                                    $table_video_insert = mysqli_prepare($connection, "INSERT INTO VS_VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, VIDEO_LOCATION, VIDEO_SIZE, VS_USER_ID) VALUES ('?', '?', '?', '?', ?)");
                                    mysqli_stmt_bind_param($table_video_insert, 'ssssi', $video_title, $video_description, $uploaded_video_name, $video_media_size, $user_id);
                                    mysqli_stmt_execute($table_video_insert);

                                    if (mysqli_affected_rows($connection) > 0) {
                                        array_push($response["log"], date('H:i:s') . ": inserted new video: " . $username);

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
            } else if ($action == "comment") {
            } else if ($action == "feedback") {
            } else if ($action == "follow") {
            }
        }
    }

    // PUT-Options:
    // TODO
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD'];
        if (isset($_POST["medium"])) {
            $medium = $_POST["medium"];
            if ($medium == "profile_username") {
            } else if ($medium == "profile_password") {
            } else if ($medium == "profile_description") {
            } else if ($medium == "profile_socials") {
            } else if ($medium == "profile_picture") {
                if (isset($_FILES["IMAGE_MEDIA"])) { // TODO: Database Save
                    $supportet_image_formats = array(
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
                    if ($_FILES["VIDEO_MEDIA"]["error"] == 0 && in_array(strtolower($video_media_type), array_map('strtolower', $supportet_image_formats))) {
                        switch ($type) {
                            case 'jpg':
                            case 'jpeg':
                            case 'jpe':
                            case 'jif':
                            case 'jfif':
                            case 'jfi':
                                $image = imagecreatefromjpeg($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                imagejpeg(imagescale($image, $width, $height), $media_image_path);
                                break;
                            case 'png':
                                $image = imagecreatefrompng($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                imagepng(imagescale($image, $width, $height), $media_image_path);
                                break;
                            case 'gif':
                                $image = imagecreatefromgif($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                imagegif(imagescale($image, $width, $height), $media_image_path);
                                break;
                            case 'bmp':
                            case 'dib':
                                $image = imagecreatefrombmp($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                imagebmp(imagescale($image, $width, $height), $media_image_path);
                                break;
                            case 'webp':
                                $image = imagecreatefromwebp($_FILES["IMAGE_MEDIA"]["tmp_name"]);
                                imagewebp(imagescale($image, $width, $height), $media_image_path);
                                break;
                            default:
                                break;
                        }
                    }
                }
            } else if ($medium == "video_post_title") {
            } else if ($medium == "video_post_description") {
            } else if ($medium == "video_post_hashtags") {
            } else if ($medium == "comment_post_text") {
            }
        }
    }

    // DELETE-Options:
    // TODO
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD'];
        if (isset($_POST["medium"])) {
            $medium = $_POST["medium"];
            if ($medium == "all") {
            } else if ($medium == "account") {
            } else if ($medium == "video") {
            } else if ($medium == "comment") {
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
