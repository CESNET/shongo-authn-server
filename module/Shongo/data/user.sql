--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `given_name` varchar(32) NOT NULL,
  `family_name` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `original_id` varchar(255) NOT NULL,
  `register_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `original_id` (`original_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;