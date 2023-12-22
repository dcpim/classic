## Health module

This page displays medical document along with various graphs related to food consumption and Apple healthkit export.

It requires the following database tables:

### foods
```
CREATE TABLE `foods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) DEFAULT NULL,
  `fiber` int(11) DEFAULT NULL,
  `sugar` int(11) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### nutrition
```
CREATE TABLE `nutrition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `food` int(11) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1921 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### health
```
CREATE TABLE `health` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `distance` float(12,2) DEFAULT NULL,
  `stairs` int(11) DEFAULT NULL,
  `steps` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `diastolic` int(11) DEFAULT NULL,
  `systolic` int(11) DEFAULT NULL,
  `heart` int(11) DEFAULT NULL,
  `fat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=123663 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### medical_files
```
CREATE TABLE `medical_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(150) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### health_info 
```
CREATE TABLE `health_info` (
  `line1` varchar(2000) DEFAULT NULL,
  `line2` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

