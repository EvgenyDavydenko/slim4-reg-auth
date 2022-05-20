CREATE DATABASE `slim4-reg-auth`;

CREATE TABLE `user`
(
    user_id  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    email    VARCHAR(255)     NOT NULL,
    username VARCHAR(255)     NOT NULL,
    password VARCHAR(255)     NOT NULL,
    PRIMARY KEY (user_id)
) ENGINE = InnoDB;
