-- Used to Create Database

CREATE TABLE account (
    user_id INT(11) PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    first_name VARCHAR(26),
    last_name VARCHAR(26),
    user_role ENUM('admin', 'standard') NOT NULL DEFAULT 'standard',
    banned boolean DEFAULT 0,
    number_of_reports INT(11) DEFAULT 0
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
    dateOfUnban DATE,
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
    response_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    viewed_by_target BOOLEAN NOT NULL DEFAULT FALSE,
    viewed_by_initiator BOOLEAN NOT NULL DEFAULT FALSE,
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