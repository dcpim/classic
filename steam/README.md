## Steam module

This module provides a list of Steam games along with played time, rating, etc.

It requires this database table:

### steam
```
CREATE TABLE `steam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_name` varchar(50) DEFAULT NULL,
  `appid` int(11) NOT NULL,
  `played_time` int(11) DEFAULT NULL,
  `date` varchar(20) NOT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `release_date` varchar(50) DEFAULT NULL,
  `stars` int(11) DEFAULT NULL,
  `review` varchar(2000) DEFAULT NULL,
  `hidden` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appid` (`appid`)
) ENGINE=InnoDB AUTO_INCREMENT=492797 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
