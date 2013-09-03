CREATE TABLE `user` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`email` varchar(256) NOT NULL,
`name` varchar(256) DEFAULT NULL,
`hash` varchar(256) NOT NULL,
`login_hash` varchar(256) DEFAULT NULL,
`role` varchar(8) NOT NULL,
`state` varchar(8) NOT NULL,
`display_name` varchar(256) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB;