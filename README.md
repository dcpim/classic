# DCPIM v2.x

This repo contains the code for DCPIM v2.x, a Personal Information Management (PIM) web app built in a classic style, using PHP code for the frontend and Python code for the backend scripts, both running as CGI scripts under an Apache web server. It consists of a core system and several optional modules for things such as photos, videos, music, projects, and much more.


## Requirements

The following software requirements are needed:

* Apache web server
* MySQL database server

These environment variables need to be added to the Apache config in order for the scripts to connect to the database:

Also, several tables must be created and populated in the database. Note that each module also have database tables, documented in each of their folders.

### users
```
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `admin` int(11) NOT NULL,
  `sms` varchar(100) DEFAULT NULL,
  `last_reset` varchar(20) DEFAULT NULL,
  `totp` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### config
```
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `k` varchar(50) NOT NULL,
  `v` varchar(2000) DEFAULT NULL,
  `notes` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### sessions
```
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `valid_until` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `admin` int(11) NOT NULL,
  `browser` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8399 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### token
```
CREATE TABLE `token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curtoken` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108748 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

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
) ENGINE=InnoDB AUTO_INCREMENT=7366771 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```


## Installation

The repo can be cloned and the script `deploy.py` can be used to deploy the files on the web server.


## Authors

This code was created by: Patrick Lambert patrick@dendory.ca


## License

Copyright 2018-2024, Patrick Lambert

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

