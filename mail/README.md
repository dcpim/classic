## Mail module

This module is used with the Postfix mail server and pipelines to provide domain a blacklist and logging delivery failures.

It requires the following tables:

### mail_blacklist
```
CREATE TABLE `mail_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `date` varchar(20) DEFAULT NULL,
  `hits` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### mail_failures
```
CREATE TABLE `mail_failures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) DEFAULT NULL,
  `message` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `msg_uniq` (`date`,`message`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=11912 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

