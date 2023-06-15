<?php

// phpinfo();

// create guest user
$guest_user = "CREATE USER IF NOT EXISTS '$guest_user_username'@'%' IDENTIFIED WITH mysql_native_password BY '$guest_user_password'"; // with mysql_native_password to avoid PHP problems
$guest_user_query = mysqli_query($connection, $guest_user);

if ($guest_user_query) {
    array_push($response["log"], date('H:i:s') . ": vidslide guest user created/found " . "[$guest_user_query]");
} else {
    errorOccurred($connection, $response, __LINE__, "couldn't create guest user");
}

// get privileges of guest user
$grantee = "guest'@'%";
$guest_user_privileges = "SELECT COUNT(*) as privileges FROM INFORMATION_SCHEMA.USER_PRIVILEGES WHERE GRANTEE = \"'guest'@'%'\" AND PRIVILEGE_TYPE = 'SELECT'";
$guest_user_privileges_query = mysqli_query($connection, $guest_user_privileges);
$guest_user_privileges_row = mysqli_fetch_assoc($guest_user_privileges_query);
$guest_user_privilege_count = $guest_user_privileges_row['privileges'];

if ($guest_user_privilege_count == 0) {
    array_push($response["log"], date('H:i:s') . ": vidslide guest user privileges fetched " . "[$guest_user_privilege_count]");

    mysqli_free_result($guest_user_privileges_query);

    // set guest user privileges => READ ONLY (SELECT)
    $guest_user_grant_privileges = "GRANT SELECT ON *.* TO '$guest_user_username'@'%'";
    $guest_user_grant_privileges_query = mysqli_query($connection, $guest_user_grant_privileges);

    if ($guest_user_grant_privileges_query) {
        array_push($response["log"], date('H:i:s') . ": vidslide guest user privileges set " . "[$guest_user_grant_privileges_query]");
    } else {
        errorOccurred($connection, $response, __LINE__, "couldn't asign guest user privileges");
    }
} else if ($guest_user_privilege_count == 1) {
    array_push($response["log"], date('H:i:s') . ": vidslide guest user privileges fetched " . "[$guest_user_privilege_count]");
} else {
    errorOccurred($connection, $response, __LINE__, "couldn't fetch guest user privileges");
}

// create database
$create_schema = "CREATE DATABASE IF NOT EXISTS $schema";
$schema_query = mysqli_query($connection, $create_schema);

if ($schema_query) {
    array_push($response["log"], date('H:i:s') . ": vidslide database created/found " . "[$schema_query]");
    mysqli_select_db($connection, $schema);
} else {
    errorOccurred($connection, $response, __LINE__, "couldn't create database");
}

// create tables
array_push($response["log"], date('H:i:s') . ": looking for tables...");

$table_01 = "CREATE TABLE IF NOT EXISTS VS_USER (
        VS_USER_ID INT AUTO_INCREMENT PRIMARY KEY,
        USER_USERNAME VARCHAR(25) NOT NULL,
        USER_PASSWORD VARCHAR(250) NOT NULL,
        USER_PROFILEPICTURE VARCHAR(100) DEFAULT NULL,
        USER_PROFILEDESCRIPTION VARCHAR(1000) DEFAULT NULL,
        USER_DATETIMECREATED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        USER_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT UNIQUE_USERNAME UNIQUE (USER_USERNAME)
    )";

$table_02 = "CREATE TABLE IF NOT EXISTS VS_VIDEO (
        VS_VIDEO_ID INT AUTO_INCREMENT PRIMARY KEY,
        VIDEO_TITLE VARCHAR(25) NOT NULL,
        VIDEO_DESCRIPTION VARCHAR(500) DEFAULT NULL,
        VIDEO_LOCATION VARCHAR(250) NOT NULL,
        VIDEO_SIZE VARCHAR(6) NOT NULL,
        VIDEO_VIEWS INT DEFAULT 0,
        VIDEO_SHARES INT DEFAULT 0,
        VIDEO_DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        VIDEO_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        VS_USER_ID INT NOT NULL,
        FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID)
    )";

$table_03 = "CREATE TABLE IF NOT EXISTS VS_USER_SOCIAL (
        SOCIAL_ID INT AUTO_INCREMENT PRIMARY KEY,
        SOCIAL_PLATFORM VARCHAR(25) NOT NULL,
        SOCIAL_URL VARCHAR(250) NOT NULL,
        VS_USER_ID INT NOT NULL,
        FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID),
        CONSTRAINT UNIQUE_SOCIAL UNIQUE (SOCIAL_PLATFORM, SOCIAL_URL, VS_USER_ID)
    )";

$table_04 = "CREATE TABLE IF NOT EXISTS VS_USER_FOLLOWING (
        FOLLOWING_ID INT AUTO_INCREMENT PRIMARY KEY,
        FOLLOWING_SUBSCRIBER INT NOT NULL,
        FOLLOWING_SUBSCRIBED INT NOT NULL,
        FOREIGN KEY (FOLLOWING_SUBSCRIBER) REFERENCES VS_USER(VS_USER_ID),
        FOREIGN KEY (FOLLOWING_SUBSCRIBED) REFERENCES VS_USER(VS_USER_ID),
        CONSTRAINT UNIQUE_FOLLOWING UNIQUE (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED)
    )";

$table_05 = "CREATE TABLE IF NOT EXISTS VS_VIDEO_FEEDBACK (
        VIDEO_FEEDBACK_ID INT AUTO_INCREMENT PRIMARY KEY,
        VIDEO_FEEDBACK_TYPE ENUM('positive', 'negative') NOT NULL,
        VS_VIDEO_ID INT NOT NULL,
        VS_USER_ID INT NOT NULL,
        FOREIGN KEY (VS_VIDEO_ID) REFERENCES VS_VIDEO(VS_VIDEO_ID),
        FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID),
        CONSTRAINT UNIQUE_VIDEO_FEEDBACK UNIQUE (VS_VIDEO_ID, VS_USER_ID)
    )";

$table_06 = "CREATE TABLE IF NOT EXISTS VS_VIDEO_COMMENT (
        COMMENT_ID INT AUTO_INCREMENT PRIMARY KEY,
        COMMENT_PARENT_ID INT DEFAULT NULL,
        COMMENT_MESSAGE VARCHAR(250) NOT NULL,
        COMMENT_DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        COMMENT_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        VS_VIDEO_ID INT NOT NULL,
        VS_USER_ID INT NOT NULL,
        FOREIGN KEY (VS_VIDEO_ID) REFERENCES VS_VIDEO(VS_VIDEO_ID),
        FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID)
    )";

$table_07 = "CREATE TABLE IF NOT EXISTS VS_COMMENT_FEEDBACK (
        COMMENT_FEEDBACK_ID INT AUTO_INCREMENT PRIMARY KEY,
        COMMENT_FEEDBACK_TYPE ENUM('positive', 'negative') NOT NULL,
        COMMENT_ID INT NOT NULL,
        VS_USER_ID INT NOT NULL,
        FOREIGN KEY (COMMENT_ID) REFERENCES VS_VIDEO_COMMENT(COMMENT_ID),
        CONSTRAINT UNIQUE_COMMENT_FEEDBACK UNIQUE (COMMENT_ID, VS_USER_ID)
    )";

$table_08 = "CREATE TABLE IF NOT EXISTS VS_VIDEO_HASHTAG (
        HASHTAG_ID INT AUTO_INCREMENT PRIMARY KEY,
        HASHTAG_NAME VARCHAR(15) NOT NULL,
        VS_VIDEO_ID INT NOT NULL,
        FOREIGN KEY (VS_VIDEO_ID) REFERENCES VS_VIDEO(VS_VIDEO_ID),
        CONSTRAINT UNIQUE_HASHTAG UNIQUE (VS_VIDEO_ID, HASHTAG_NAME)
    )";

for ($i = 1; $i <= 8; $i++) {
    $table_create_query = mysqli_query($connection, ${"table_" . str_pad(strval($i), 2, "0", STR_PAD_LEFT)});
    if ($table_create_query) {
        array_push($response["log"], date('H:i:s') . ": table " . $i . " created/found");
    } else {
        errorOccurred($connection, $response, __LINE__, "couldn't create table " . $i);
    }
}

// create mock data
array_push($response["log"], date('H:i:s') . ": inserting mock data...");

$table_names = array("VS_USER", "VS_VIDEO", "VS_USER_SOCIAL", "VS_USER_FOLLOWING", "VS_VIDEO_FEEDBACK", "VS_VIDEO_HASHTAG", "VS_VIDEO_COMMENT", "VS_COMMENT_FEEDBACK");
$mock_data = array(
    "INSERT IGNORE INTO VS_USER (USER_USERNAME, USER_PASSWORD) VALUES ('JohnDoe', 'password123');
    INSERT IGNORE INTO VS_USER (USER_USERNAME, USER_PASSWORD, USER_PROFILEPICTURE, USER_PROFILEDESCRIPTION) VALUES ('JaneDoe', 'password456', 'https://picsum.photos/50/50', 'Hi, I love coding!');
    INSERT IGNORE INTO VS_USER (USER_USERNAME, USER_PASSWORD, USER_PROFILEPICTURE) VALUES ('AlexSmith', 'password789', 'https://picsum.photos/50/50');
    INSERT IGNORE INTO VS_USER (USER_USERNAME, USER_PASSWORD, USER_PROFILEDESCRIPTION) VALUES ('MeganTaylor', 'password111', 'I am a designer and love creating beautiful things!');
    INSERT IGNORE INTO VS_USER (USER_USERNAME, USER_PASSWORD, USER_PROFILEPICTURE, USER_PROFILEDESCRIPTION) VALUES ('SamJones', 'password222', 'https://picsum.photos/50/50', 'I am an entrepreneur and love building businesses!');",
    "INSERT IGNORE INTO VS_VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, VIDEO_LOCATION, VIDEO_SIZE, VS_USER_ID) VALUES ('Mein erster Vlog', 'Hier ist mein erster Vlog, den ich jemals gemacht habe!', 'vid_1.MP4', '25MB', 5);
    INSERT IGNORE INTO VS_VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, VIDEO_LOCATION, VIDEO_SIZE, VS_USER_ID) VALUES ('My Home Movie', 'A fun family outing', 'vid_2.MP4', '100MB', 1);
    INSERT IGNORE INTO VS_VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, VIDEO_LOCATION, VIDEO_SIZE, VS_USER_ID) VALUES ('Funny Cat Video', 'A hilarious compilation of cats being silly', 'vid_3.MP4', '50MB', 2);
    INSERT IGNORE INTO VS_VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, VIDEO_LOCATION, VIDEO_SIZE, VS_USER_ID) VALUES ('My Vacation in Hawaii', 'A trip to remember', 'vid_4.MP4', '500MB', 3);
    INSERT IGNORE INTO VS_VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, VIDEO_LOCATION, VIDEO_SIZE, VS_USER_ID) VALUES ('My First Concert', 'My band is first gig', 'vid_5.MP4', '200MB', 4)",
    "INSERT IGNORE INTO VS_USER_SOCIAL (SOCIAL_PLATFORM, SOCIAL_URL, VS_USER_ID) VALUES ('Github', 'https://github.com/jonasfroeller', 1);
    INSERT IGNORE INTO VS_USER_SOCIAL (SOCIAL_PLATFORM, SOCIAL_URL, VS_USER_ID) VALUES ('Instagram', 'https://www.instagram.com/user2/', 2);
    INSERT IGNORE INTO VS_USER_SOCIAL (SOCIAL_PLATFORM, SOCIAL_URL, VS_USER_ID) VALUES ('YouTube', 'https://www.youtube.com/user3/', 3); 
    INSERT IGNORE INTO VS_USER_SOCIAL (SOCIAL_PLATFORM, SOCIAL_URL, VS_USER_ID) VALUES ('LinkedIn', 'https://www.linkedin.com/in/user4/', 4);
    INSERT IGNORE INTO VS_USER_SOCIAL (SOCIAL_PLATFORM, SOCIAL_URL, VS_USER_ID) VALUES ('Twitter', 'https://github.com/jonasfroeller', 5);",
    "INSERT IGNORE INTO VS_USER_FOLLOWING (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED) VALUES (1, 2);
    INSERT IGNORE INTO VS_USER_FOLLOWING (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED) VALUES (3, 1);
    INSERT IGNORE INTO VS_USER_FOLLOWING (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED) VALUES (2, 4);
    INSERT IGNORE INTO VS_USER_FOLLOWING (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED) VALUES (4, 1);
    INSERT IGNORE INTO VS_USER_FOLLOWING (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED) VALUES (2, 1);",
    "INSERT IGNORE INTO VS_VIDEO_FEEDBACK (VIDEO_FEEDBACK_TYPE, VS_VIDEO_ID, VS_USER_ID) VALUES ('positive', 1, 4);
    INSERT IGNORE INTO VS_VIDEO_FEEDBACK (VIDEO_FEEDBACK_TYPE, VS_VIDEO_ID, VS_USER_ID) VALUES ('positive', 1, 2);
    INSERT IGNORE INTO VS_VIDEO_FEEDBACK (VIDEO_FEEDBACK_TYPE, VS_VIDEO_ID, VS_USER_ID) VALUES ('negative', 3, 4);
    INSERT IGNORE INTO VS_VIDEO_FEEDBACK (VIDEO_FEEDBACK_TYPE, VS_VIDEO_ID, VS_USER_ID) VALUES ('positive', 2, 5);
    INSERT IGNORE INTO VS_VIDEO_FEEDBACK (VIDEO_FEEDBACK_TYPE, VS_VIDEO_ID, VS_USER_ID) VALUES ('negative', 4, 1);",
    "INSERT IGNORE INTO VS_VIDEO_HASHTAG (HASHTAG_NAME, VS_VIDEO_ID) VALUES ('Vlog', 1);
    INSERT IGNORE INTO VS_VIDEO_HASHTAG (HASHTAG_NAME, VS_VIDEO_ID) VALUES ('lustig', 1);
    INSERT IGNORE INTO VS_VIDEO_HASHTAG (HASHTAG_NAME, VS_VIDEO_ID) VALUES ('reisen', 2), ('Urlaub', 2);
    INSERT IGNORE INTO VS_VIDEO_HASHTAG (HASHTAG_NAME, VS_VIDEO_ID) VALUES ('musik', 3);
    INSERT IGNORE INTO VS_VIDEO_HASHTAG (HASHTAG_NAME, VS_VIDEO_ID) VALUES ('kochen', 4), ('gesund', 4), ('friday', 4);",
    "INSERT IGNORE INTO VS_VIDEO_COMMENT (COMMENT_MESSAGE, VS_VIDEO_ID, VS_USER_ID) VALUES ('Tolles Video!', 1, 3);
    INSERT IGNORE INTO VS_VIDEO_COMMENT (COMMENT_MESSAGE, VS_VIDEO_ID, VS_USER_ID) VALUES ('Great video!', 1, 2);
    INSERT IGNORE INTO VS_VIDEO_COMMENT (COMMENT_PARENT_ID, COMMENT_MESSAGE, VS_VIDEO_ID, VS_USER_ID) VALUES (1, 'Thanks for your comment!', 1, 1);
    INSERT IGNORE INTO VS_VIDEO_COMMENT (COMMENT_MESSAGE, VS_VIDEO_ID, VS_USER_ID) VALUES ('This video is amazing!', 4, 3);
    INSERT IGNORE INTO VS_VIDEO_COMMENT (COMMENT_PARENT_ID, COMMENT_MESSAGE, VS_VIDEO_ID, VS_USER_ID) VALUES (1, 'I completely agree!', 1, 2);",
    "INSERT IGNORE INTO VS_COMMENT_FEEDBACK (COMMENT_FEEDBACK_TYPE, COMMENT_ID, VS_USER_ID) VALUES ('negative', 1, 1);
    INSERT IGNORE INTO VS_COMMENT_FEEDBACK (COMMENT_FEEDBACK_TYPE, COMMENT_ID, VS_USER_ID) VALUES ('positive', 2, 1);
    INSERT IGNORE INTO VS_COMMENT_FEEDBACK (COMMENT_FEEDBACK_TYPE, COMMENT_ID, VS_USER_ID) VALUES ('negative', 2, 3);
    INSERT IGNORE INTO VS_COMMENT_FEEDBACK (COMMENT_FEEDBACK_TYPE, COMMENT_ID, VS_USER_ID) VALUES ('positive', 4, 2);
    INSERT IGNORE INTO VS_COMMENT_FEEDBACK (COMMENT_FEEDBACK_TYPE, COMMENT_ID, VS_USER_ID) VALUES ('negative', 3, 4);"
);

for ($i = 0; $i < count($mock_data); $i++) {
    $table_name = $table_names[$i];
    $count = "SELECT COUNT(*) as amount FROM $table_name"; // returns 0 => empty || IGNORE => ignores if UNIQUE
    $count_query = mysqli_query($connection, $count);
    $count_fetched = mysqli_fetch_assoc($count_query);
    mysqli_free_result($count_query);

    $table_nr = $i + 1;
    if (intval($count_fetched["amount"]) == 0) {
        mysqli_multi_query($connection, $mock_data[$i]);

        while (mysqli_next_result($connection)) {
        } // mysqli_multi_query is asyncronous!!! => causes out of sync problem if not awaited // don't allow ' in text or escape it! 

        array_push($response["log"], date('H:i:s') . ": inserted mock data for table $table_nr");
    } else {
        array_push($response["log"], date('H:i:s') . ": found mock data for table $table_nr in database");
    }
}

array_push($response["log"], date('H:i:s') . ": finished database initialisation");

// check if server is alive
if (mysqli_ping($connection)) {
    array_push($response["log"], date('H:i:s') . ": connection ok");
} else {
    errorOccurred($connection, $response, __LINE__, "database ping failed");
}

// export_database($connection);