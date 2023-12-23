## Status module

This module provides a status screen for a display monitor to use, providing information such as news, weather and stocks.

It requires this database table:

### nutrition_stats
```
CREATE TABLE `nutrition_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internalizing` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `internalizing` (`internalizing`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
