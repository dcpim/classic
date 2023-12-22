## Income module

This page handles personal finances such as spendings, income and savings. Note that it is highly customized to the Canadian market and may need to be adjusted for other purposes.

The following database table is required:

### income
```
CREATE TABLE `income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note` varchar(100) NOT NULL,
  `date` varchar(20) NOT NULL,
  `credit` float(8,2) DEFAULT NULL,
  `debit` float(8,2) DEFAULT NULL,
  `is_saving` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
