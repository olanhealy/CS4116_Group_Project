-- Used to Create Database

CREATE TABLE account (
    user_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    first_name VARCHAR(26),
    last_name VARCHAR(26),
    user_role ENUM('admin', 'standard') NOT NULL DEFAULT 'standard',
    banned boolean DEFAULT 0
    );

CREATE TABLE profile (
    user_id INT(11) PRIMARY KEY,
    name VARCHAR(26),
    age TINYINT UNSIGNED,
    gender ENUM('Male', 'Female', 'Other'),
    bio TEXT,
    profile_pic VARCHAR(255),
    pursuing ENUM('Male', 'Female', 'Other'),
    verified boolean,
    college_year ENUM('Undergrad', 'Masters', 'PhD'),
    course VARCHAR(26),
    hobbies VARCHAR(50),
    looking_for ENUM('Short-term', 'Long-term', 'Unsure'),
    FOREIGN KEY (user_id) REFERENCES Account(user_id)
);

CREATE TABLE banned(
    user_id INT(11) PRIMARY KEY,
    banned_by INT(11),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reason TEXT,
    duration TIME,
    FOREIGN KEY (user_id) REFERENCES Account(user_id),
    FOREIGN KEY (banned_by) REFERENCES Account(user_id)
);

CREATE TABLE adore(
    adore_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    adored_user_id INT(11),
    FOREIGN KEY (adored_user_id) REFERENCES Account(user_id),
    FOREIGN KEY (user_id) REFERENCES Account(user_id)
);

CREATE TABLE `ignore` (
    ignore_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ignored_user_id INT(11),
    FOREIGN KEY (ignored_user_id) REFERENCES Account(user_id),
    FOREIGN KEY (user_id) REFERENCES Account(user_id)
);

CREATE TABLE matches (
    match_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    initiator_id INT(11),
    target_id INT(11),
    `status` ENUM('Adore', 'Ignore', 'Pending') NOT NULL,
    response_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (initiator_id) REFERENCES Account(user_id),
    FOREIGN KEY (target_id) REFERENCES Account(user_id)
);

CREATE TABLE messages(
    message_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    match_id INT(11),
    receiver_id INT(11),
    sender_id INT(11),
    message_content TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_status ENUM('read','delivered'),
    FOREIGN KEY (match_id) REFERENCES Matches(match_id),
    FOREIGN KEY (receiver_id) REFERENCES Account(user_id),
    FOREIGN KEY (sender_id) REFERENCES Account(user_id)
);


-- Dummy data for Account table
INSERT INTO account (email, password_hash, first_name, last_name, user_role, banned)
VALUES ('21344256@studentmail.ul.ie', 'hashed_password', 'Kevin', 'Collins', 'standard', 0),
       ('21344257@studentmail.ul.ie', 'hashed_password', 'Olan', 'Healy', 'admin', 1);

-- Dummy data for Profile table
INSERT INTO profile (user_id, name, age, gender, bio, profile_pic, pursuing, verified, college_year, course, hobbies, looking_for)
VALUES (1, 'Kevin Collins', 30, 'Female', 'Im an idiot', 'profile_pic.jpg', 'Male', 1, 'Masters', 'Computer Science', 'Reading, Hiking', 'Long-term'),
       (2, 'Olan Healy', 28, 'Male', 'Im a legend', 'profile_pic.jpg', 'Female', 1, 'Undergrad', 'Psychology', 'Traveling, Photography', 'Unsure');

-- Dummy data for Banned table
INSERT INTO banned (user_id, banned_by, reason, duration)
VALUES (2, 1, 'Inappropriate behavior', '24:00:00');

-- Dummy data for Adore table
INSERT INTO adore (user_id, adored_user_id)
VALUES (1, 2), (2, 1);

-- Dummy data for Ignore table
INSERT INTO `ignore` (user_id, ignored_user_id)
VALUES (1, 2), (2, 1);

-- Dummy data for Matches table
INSERT INTO matches (initiator_id, target_id, `status`, response_date)
VALUES (1, 2, 'Adore', NOW()), (2, 1, 'Ignore', NOW());

-- Dummy data for Messages table
INSERT INTO messages (match_id, receiver_id, sender_id, message_content, read_status)
VALUES (1, 2, 1, 'Hello Olan, how are you?', 'delivered'),
       (1, 1, 2, 'Hi Kevin, Im doing well, thanks!', 'read');