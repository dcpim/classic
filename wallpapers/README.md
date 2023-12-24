## Wallpapers module

This module is meant to create a collection of anime wallpapers.

It requires the following database table:

### wallpapers
```
CREATE TABLE `wallpapers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `url` varchar(150) NOT NULL,
  `thumb` varchar(150) NOT NULL,
  `size` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `landscape` int(11) DEFAULT NULL,
  `tag_action` int(11) DEFAULT NULL,
  `tag_bondage` int(11) DEFAULT NULL,
  `tag_casual` int(11) DEFAULT NULL,
  `tag_jk` int(11) DEFAULT NULL,
  `tag_loli` int(11) DEFAULT NULL,
  `tag_muscles` int(11) DEFAULT NULL,
  `tag_nsfw` int(11) DEFAULT NULL,
  `tag_yuri` int(11) DEFAULT NULL,
  `labels` varchar(5000) DEFAULT NULL,
  `tag_ai` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  UNIQUE KEY `thumb` (`thumb`)
) ENGINE=InnoDB AUTO_INCREMENT=1767 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
