## Music module

This module fetches music files from YouTube, converts them to MP3 files and gather metadata such as size and length.

It requires the following database table:

### music
```
CREATE TABLE `music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `artist` varchar(30) DEFAULT NULL,
  `url` varchar(150) NOT NULL,
  `size` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `duration` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=1542 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
