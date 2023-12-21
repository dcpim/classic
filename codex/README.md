## Codex module

The Codex module is used to store and display code snippets with syntax highlighting.

It requires this database table:

### code
```
CREATE TABLE `code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(150) NOT NULL,
  `language` varchar(20) NOT NULL,
  `content` longtext DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `prjid` int(11) DEFAULT NULL,
  `sync` varchar(250) DEFAULT NULL,
  `pub` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `desc_index` (`description`,`prjid`)
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

