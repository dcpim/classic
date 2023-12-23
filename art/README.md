## Art module

This module allows the upload of images and shows them as a gallery in sections.

It requires the following table:

### renders
```
 CREATE TABLE `renders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `genre` varchar(20) DEFAULT NULL,
  `url` varchar(150) NOT NULL,
  `thumb` varchar(150) NOT NULL,
  `size` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `description` varchar(20000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  UNIQUE KEY `thumb` (`thumb`)
) ENGINE=InnoDB AUTO_INCREMENT=1260 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci
```

