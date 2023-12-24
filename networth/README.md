## Networth module

This module is made to keep track of personal networth, by adding things like investments, physical assets, etc.

It requires the following table:

### networth
```
CREATE TABLE `networth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `banks` float(9,2) DEFAULT NULL,
  `investments` float(9,2) DEFAULT NULL,
  `assets` float(9,2) DEFAULT NULL,
  `equity` float(9,2) DEFAULT NULL,
  `cc` float(9,2) DEFAULT NULL,
  `mortgage` float(9,2) DEFAULT NULL,
  `loans` float(9,2) DEFAULT NULL,
  `biz` float(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
