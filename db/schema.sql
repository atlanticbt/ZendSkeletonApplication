CREATE TABLE `user` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`email` varchar(256) NOT NULL,
`name` varchar(256) DEFAULT NULL,
`hash` varchar(256) NOT NULL,
`login_hash` varchar(256) DEFAULT NULL,
`role` varchar(8) NOT NULL,
`state` varchar(8) NOT NULL,
`display_name` varchar(256) DEFAULT NULL,
`created_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`last_modified_ts` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB;