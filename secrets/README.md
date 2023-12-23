## Secrets module

This module provides a way to vault secrets, with a client side master key using AES encryption.

It requires this database table:

### secrets
```
CREATE TABLE `secrets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site` varchar(100) NOT NULL,
  `secret` varchar(1000) DEFAULT NULL,
  `note` varchar(200) DEFAULT NULL,
  `date` varchar(20) NOT NULL,
  `prjid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site` (`site`)
) ENGINE=InnoDB AUTO_INCREMENT=338 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
