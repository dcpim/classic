## Files module

This module allows uploading files, sharing files, making a PDF from an archive of files, etc.

Create the folder `/uploads` and `/var/www/html/share` and set them chmod 777.

It also requires the following table:

### utils
```
CREATE TABLE `utils` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `url` varchar(150) NOT NULL,
  `size` bigint(20) NOT NULL,
  `date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=844 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
