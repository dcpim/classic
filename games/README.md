## Games module

This module provides a gallery of gaming screenshots.

It requires the following database table:

### games
```
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game` varchar(50) NOT NULL,
  `url` varchar(150) NOT NULL,
  `thumb` varchar(150) NOT NULL,
  `size` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  UNIQUE KEY `thumb` (`thumb`)
) ENGINE=InnoDB AUTO_INCREMENT=733 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
