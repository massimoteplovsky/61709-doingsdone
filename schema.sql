CREATE DATABASE `doingsdone`;
USE `doingsdone`;
CREATE TABLE `projects` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`name` CHAR(255) NOT NULL,
`user_id` INT NOT NULL
);
CREATE TABLE `tasks` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`user_id` INT NOT NULL,
`project_id` INT NOT NULL,
`created` DATETIME NOT NULL,
`complete` DATETIME DEFAULT NULL,
`deadline` DATETIME,
`name` CHAR(255) NOT NULL,
`file` CHAR(255)
);
CREATE TABLE `user` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`registered` DATETIME NOT NULL,
`email`CHAR(155) NOT NULL,
`name` CHAR(155) NOT NULL,
`avatar` CHAR(100),
`password` VARCHAR(255) NOT NULL
);
CREATE UNIQUE INDEX project_fields ON projects(user_id, name);
CREATE UNIQUE INDEX task_fields ON user(email, password);
CREATE INDEX user ON projects(user_id);
CREATE INDEX user ON tasks(user_id);
CREATE INDEX project ON tasks(project_id);