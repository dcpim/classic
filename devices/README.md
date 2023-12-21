## Devices module

This page shows information gathered by multiple pipelines, includes scanning LAN devices, web and S3 size stats, etc.

It requires the following database tables:

### device_stats
```
CREATE TABLE `device_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device` varchar(100) NOT NULL,
  `disk` varchar(20) NOT NULL,
  `diskusage` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `updatedate` varchar(50) DEFAULT NULL,
  `uptime` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=574 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### db_storage
```
CREATE TABLE `db_storage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trows` bigint(20) DEFAULT NULL,
  `tsize` bigint(20) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### s3_storage
```
CREATE TABLE `s3_storage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bucket` varchar(50) NOT NULL,
  `date` varchar(20) NOT NULL,
  `ObjectCount` bigint(20) DEFAULT NULL,
  `DeleteMarkerObjectCount` bigint(20) DEFAULT NULL,
  `NonCurrentVersionObjectCount` bigint(20) DEFAULT NULL,
  `StorageBytes` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`bucket`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=532 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### s3_logs
```
CREATE TABLE `s3_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `query` varchar(50) NOT NULL,
  `object` varchar(100) NOT NULL,
  `code` int(11) NOT NULL,
  `country` varchar(5) DEFAULT NULL,
  `orgname` varchar(100) DEFAULT NULL,
  `bucket` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`date`,`ip`,`query`,`object`)
) ENGINE=InnoDB AUTO_INCREMENT=612797 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### wwwlogs
```
CREATE TABLE `wwwlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `code` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `url` varchar(200) NOT NULL,
  `country` varchar(5) DEFAULT NULL,
  `orgname` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`date`,`code`,`ip`,`url`)
) ENGINE=InnoDB AUTO_INCREMENT=138008 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### wlan_scan
```
CREATE TABLE `wlan_scan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `first_seen` varchar(20) NOT NULL,
  `last_seen` varchar(20) NOT NULL,
  `notes` varchar(100) DEFAULT NULL,
  `dns` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac` (`mac`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

