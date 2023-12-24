## Photos module

This module provides a photos gallery, categorized by year and event.

It requires the following table:

### photos
```
CREATE TABLE `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `event` varchar(50) DEFAULT NULL,
  `url` varchar(150) NOT NULL,
  `thumb` varchar(150) NOT NULL,
  `size` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `device` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  UNIQUE KEY `thumb` (`thumb`)
) ENGINE=InnoDB AUTO_INCREMENT=801 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
