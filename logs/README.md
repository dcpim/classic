## Logs module

This page shows the latest entries from event logs, along with other logs collected by pipelines.

It requires the following database tables:

### log
```
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL,
  `ip` varchar(20) NOT NULL,
  `event` varchar(30) NOT NULL,
  `result` varchar(500) DEFAULT NULL,
  `date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_index` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=7481908 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### syslog
```
CREATE TABLE `syslog` (
  `id` varchar(41) NOT NULL,
  `date` varchar(20) NOT NULL,
  `process` varchar(20) NOT NULL,
  `message` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
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
) ENGINE=InnoDB AUTO_INCREMENT=144332 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
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
) ENGINE=InnoDB AUTO_INCREMENT=616711 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
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
) ENGINE=InnoDB AUTO_INCREMENT=550 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### wlan_ssid
```
CREATE TABLE `wlan_ssid` (
  `mac` varchar(20) NOT NULL,
  `channel` int(11) NOT NULL,
  `frequency` varchar(20) NOT NULL,
  `encryption` varchar(20) NOT NULL,
  `quality` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `vendor` varchar(100) NOT NULL,
  `first_seen` varchar(20) NOT NULL,
  `last_seen` varchar(20) NOT NULL,
  PRIMARY KEY (`mac`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

