## Videos module

Upload videos, download them from YouTube, add large videos from S3 and display them on the status screen.

Create empty file `/var/www/html/localplay/play.txt` and set chmod 777.

This module requires the following table:

### videos
```
CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(120) NOT NULL,
  `type` varchar(35) NOT NULL,
  `size` bigint(20) NOT NULL,
  `date` varchar(20) NOT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `thumb` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=466 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
