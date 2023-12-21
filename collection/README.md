## Collection module

This module shows collections of goods and books in 2 tables, with images, ratings and information.

It requires this database table:

### collection
```
CREATE TABLE `collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `thumb` varchar(150) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `date` varchar(20) NOT NULL,
  `sold` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `procurement` varchar(20) DEFAULT NULL,
  `stars` int(11) DEFAULT NULL,
  `subtype` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thumb` (`thumb`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=515 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
