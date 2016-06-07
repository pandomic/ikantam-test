CREATE TABLE `{{prefix}}users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(256) NOT NULL,
    `token` VARCHAR(200)
) ENGINE = InnoDb;

CREATE TABLE `{{prefix}}files` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `location` VARCHAR(200) NOT NULL,
    `uploadtime` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `token` VARCHAR(200) UNIQUE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES app_users(id)
) ENGINE = InnoDb;