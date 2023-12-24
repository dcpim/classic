# DCPIM v2.x

This repo contains the code for DCPIM v2.x, a Personal Information Management (PIM) web app developed since 2018 and built in a classic style, using PHP code for the frontend and Python code for the backend scripts, both running as CGI scripts under an Apache web server. It consists of a core system and several optional modules for things such as photos, videos, music, projects, and much more.

Note that this project was made for personal use, and deploying it for a third party may require some customization to your specific needs. It hasn't been designed to be easily portable. v3.x is currently being designed to replace this code base, using a more portable architecture around containers.


## Requirements

The following software requirements are needed:

* Apache web server
* MySQL database server
* AWS account (storage is done using S3, notifications using SNS)

Your AWS account credentials must be configured on the system, either through an instance profile or a global credentials file. You also need to create the buckets configured below and a SNS topic for notifications.

Edit `/etc/php/8.X/fpm/php.ini` and modify `upload_max_filesize = 999M`, `post_max_size = 999M`, `error_reporting = E_ERROR`, `display_errors = On`

Install the connix Python module: `pip3 install connix`

You will also need the `ffmpeg`, `ImageMagick` and `yt-dlp` packages for video and image management.

These environment variables need to be added to `/etc/apache2/sites-available/default-ssl.conf` in order for the scripts to connect to the database:

```
SetEnv DB_HOST localhost
SetEnv DB_DATABASE mydb
SetEnv DB_USER dcpm
SetEnv DB_PASS XXXXXXXXXXXXXXX
```

Also, several tables must be created and populated in the database. Note that each module also have database tables, documented in each of their folders.

Database creation:
```
CREATE DATABASE mydb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE USER dcpm@localhost IDENTIFIED BY 'XXXXXXXXXXXXXXX';
GRANT ALL PRIVILEGES ON mydb.* TO dcpm@localhost;
```

Tables creation:
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

The config table must be filled with the following values. Some of them are optional based on which module are being used.

* OPENAI_KEY: API key for OpenAI ChatGPT
* OPENAI_MODEL: OpenAI model to use for ChatGPT
* DEVICE_TOKEN: Auth token used by devices to send stats
* DATA_TOKEN: Auth token used to send scanning data
* MY_IP: Default value for my IP
* VPN_IP: Default value for VPN / Cloudfront IP
* SERVER_HOST: Name of the server host
* SERVER_IP: IP of the server host
* DB_HOST: Name of the database host
* AWS_REGION: AWS region in use
* STORAGE_HOST: Base URL for storage, with a replacable [bucket] name
* SNS_ARN: ARN to use for SNS notifications
* STEAM_KEY: Steam API key
* STEAM_ID: Steam ID
* EXPORT_PWD: Password for export 7zip file
* AUTOMATE_TOKEN: Auth token for automate agents
* AUTOMATE_BUCKET: S3 path for remote output data
* BUCKET_IMAGES: S3 bucket for images (public)
* BUCKET_VIDEOS: S3 bucket for videos (private)
* BUCKET_FILES: S3 bucket for files (semi-private)
* BUCKET_ACCOUNTING: S3 bucket for accounting (private)
* BUCKET_DATA: S3 bucket for data (private)
* BUCKET_MUSIC: S3 bucket for music (private)
* AWS_LINK: AWS console link
* REMOTE_NODE_1: Remote automation host 1
* REMOTE_NODE_2: Remote automation host 2
* DNS_DOMAIN: Domain for LAN devices

The `users` table should also be filled manually with the `admin` column being set to `1` for the site admin, and the `password` column being double SHA1 hashed. Usernames should be alphanumeric characters.


## Installation

The repo can be cloned and the script `deploy.py` can be used to deploy the files on the web server.

The file `deploy.json` contains a full list of files and where they will be deployed.

The login form is at: `https://localhost/a`


## Authors

This code was created by: Patrick Lambert patrick@dendory.ca


## License

Copyright 2018-2024, Patrick Lambert

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

