## Anime module

This module shows a list of Anime series. It provides a rating, a link for each entry and a review.

The following table is required in the database:

### series
```
CREATE TABLE `series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `review` varchar(2000) DEFAULT NULL,
  `stars` int(11) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
