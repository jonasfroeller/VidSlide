/* TABLES */

-- SHOW TABLES LIKE 'USER'; // CHECK IF EXISTS

CREATE TABLE IF NOT EXISTS USER (
    UID INT AUTO_INCREMENT PRIMARY KEY,
    USERNAME VARCHAR(25) NOT NULL,
    PASSWORD VARCHAR(25) NOT NULL,
    DATETIMECREATED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PROFILEPICTURE VARCHAR(100) DEFAULT NULL,
    PROFILEDESCRIPTION VARCHAR(1000) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS VIDEO (
    VID INT AUTO_INCREMENT PRIMARY KEY,
    UID INT NOT NULL TITLE VARCHAR(25) NOT NULL,
    DESCRIPTION VARCHAR(500) DEFAULT NULL,
    DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    VID_VIEWS INT DEFAULT 0,
    SHARES INT DEFAULT 0,
    FOREIGN KEY (UID) REFERENCES USER(UID)
);

CREATE TABLE IF NOT EXISTS USER_SOCIAL (
    SID INT AUTO_INCREMENT PRIMARY KEY,
    UID INT NOT NULL,
    PLATFORM VARCHAR(25) NOT NULL,
    URL VARCHAR(250) NOT NULL,
    FOREIGN KEY (UID) REFERENCES USER(UID)
);

CREATE TABLE IF NOT EXISTS USER_FOLLOWING (
    FID INT AUTO_INCREMENT PRIMARY KEY,
    FOLLOWER INT NOT NULL,
    FOLLOWING INT NOT NULL,
    FOREIGN KEY (FOLLOWER) REFERENCES USER(UID),
    FOREIGN KEY (FOLLOWING) REFERENCES USER(UID),
    CONSTRAINT UNIQUE_FOLLOWING UNIQUE (FOLLOWER, FOLLOWING)
);

CREATE TABLE IF NOT EXISTS VIDEO_FEEDBACK (
    FID INT AUTO_INCREMENT PRIMARY KEY,
    VID INT NOT NULL,
    FB_TYPE ENUM('positive', 'negative') NOT NULL,
    FOREIGN KEY (VID) REFERENCES VIDEO(VID)
);

CREATE TABLE IF NOT EXISTS VIDEO_HASHTAG (
    HID INT AUTO_INCREMENT PRIMARY KEY,
    VID INT NOT NULL,
    HT_NAME VARCHAR(500) NOT NULL,
    FOREIGN KEY (VID) REFERENCES VIDEO(VID),
    CONSTRAINT UNIQUE_HASHTAG UNIQUE (VID, NAME)
);

CREATE TABLE IF NOT EXISTS VIDEO_COMMENT (
    CID INT AUTO_INCREMENT PRIMARY KEY,
    PARENTCID INT,
    VID INT NOT NULL,
    UID INT NOT NULL,
    DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    TEXTCONTENT VARCHAR(250) DEFAULT NULL,
    LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (VID) REFERENCES VIDEO(VID)
);

/* INSERTS */

-- IMAGES: https://picsum.photos/50/50
-- VIDEOS: https://www.youtube.com/watch?v=dQw4w9WgXcQ
-- SOCIALS: https://github.com/jonasfroeller

-- SELECT COUNT(*) FROM table_name; returns 0 => empty

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
)