## Automate module

The automation module is a complex system scheduling pipelines to run on a schedule. It allows local and remote pipelines to be run, pass optional arguments to them, records status and output logs, sends notifications on failures, etc.

It requires the following crontab entry as whichever user should run the pipelines:

```
*/15 * * * * /var/www/html/automate/automate.py > /var/log/automate.log 2>&1
```

It also requires the following tables in the database:

### automate
```
CREATE TABLE `automate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `pipeline` varchar(30) NOT NULL,
  `params` varchar(350) DEFAULT NULL,
  `last_run` varchar(20) DEFAULT NULL,
  `next_run` int(11) DEFAULT NULL,
  `repeats` int(11) DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  `output` longtext DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `notify` int(11) DEFAULT NULL,
  `history` int(11) DEFAULT NULL,
  `node` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
```

### automate_stats
```
CREATE TABLE `automate_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(50) NOT NULL,
  `duration` int(11) NOT NULL,
  `success` int(11) NOT NULL,
  `failure` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### automate_runs
```
CREATE TABLE `automate_runs` (
  `run` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `output` longtext DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  PRIMARY KEY (`run`)
) ENGINE=InnoDB AUTO_INCREMENT=66127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

