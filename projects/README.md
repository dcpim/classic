## Projects module

The projects module contains many sub-modules that are used directly in relation to a project, such as files, secrets, tasks, notes, journal, bookmarks, bills, etc.

They require these database tables:

### projects
```
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `client` varchar(50) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `date` varchar(20) NOT NULL,
  `end_date` varchar(20) NOT NULL,
  `notes` longtext DEFAULT NULL,
  `contact_name` varchar(50) DEFAULT NULL,
  `contact_email` varchar(50) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `last_update` varchar(20) DEFAULT NULL,
  `default_hours` int(11) DEFAULT NULL,
  `default_rate` int(11) DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### project_updates
```
CREATE TABLE `project_updates` (
  `date` varchar(20) NOT NULL,
  `num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### project_notes
```
CREATE TABLE `project_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `notes` longtext DEFAULT NULL,
  `prjid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq` (`prjid`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=552 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### project_files 
```
CREATE TABLE `project_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(150) NOT NULL,
  `size` bigint(20) DEFAULT NULL,
  `date` varchar(20) NOT NULL,
  `prjid` int(11) NOT NULL,
  `notes` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=1352 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### presets
```
CREATE TABLE `presets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `name` varchar(50) NOT NULL,
  `address` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq` (`type`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1285 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### journal
```
CREATE TABLE `journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prjid` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `entry` varchar(5000) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `mood` varchar(20) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### bookmarks
```
CREATE TABLE `bookmarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prjid` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `section` varchar(200) DEFAULT NULL,
  `notes` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### bookmark_sections
```
CREATE TABLE `bookmark_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `prjid` int(11) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `multi` (`name`,`prjid`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### inventory
```
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prjid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `serial` varchar(50) DEFAULT NULL,
  `invoice` varchar(30) DEFAULT NULL,
  `price` float(8,2) DEFAULT NULL,
  `date` varchar(20) NOT NULL,
  `sold` int(11) DEFAULT NULL,
  `notes` varchar(1000) DEFAULT NULL,
  `link` varchar(400) DEFAULT NULL,
  `statement` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=855 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### billables
```
CREATE TABLE `billables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billid` int(11) NOT NULL,
  `note` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### bills
```
CREATE TABLE `bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prjid` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `tax_location` varchar(30) NOT NULL,
  `discount` float(8,2) DEFAULT NULL,
  `note` varchar(50) NOT NULL,
  `rate` int(11) DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```



