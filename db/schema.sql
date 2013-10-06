CREATE TABLE `user` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`email` varchar(256) NOT NULL,
`name` varchar(256) DEFAULT NULL,
`hash` varchar(256) DEFAULT NULL,
`login_hash` varchar(256) DEFAULT NULL,
`role` varchar(8) NOT NULL,
`state` varchar(8) NOT NULL,
`display_name` varchar(256) DEFAULT NULL,
`created_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`last_modified_ts` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE audit (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id INT(10) unsigned NOT NULL,
	type CHAR(6) NOT NULL DEFAULT 'insert',
	delta TEXT DEFAULT NULL,
	performed_ts TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	object_class VARCHAR(256) NOT NULL,
	object_id VARCHAR(256) DEFAULT NULL,
	CONSTRAINT FOREIGN KEY (user_id) REFERENCES user(id)
) ENGINE=InnoDB;
