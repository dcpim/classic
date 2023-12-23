## Statements module

This module allows the upload of accounting statements in various categories.

It requires this database table:

### statements
```
CREATE TABLE `statements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(150) NOT NULL,
  `size` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `type` varchar(50) NOT NULL,
  `scope` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=358 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
