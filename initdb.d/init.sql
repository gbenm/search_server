DROP TABLE IF EXISTS `search_logs`;
CREATE TABLE `search_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(150) NOT NULL,
  `searched_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

