## Story module

This is an experiment where users can write a sentence, and AI models will continue the story with their own sentence. It uses OpenAI ChatGPT and AWS Titan models.

It requires the following table:

### ai_story
```
CREATE TABLE `ai_story` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `sentence` varchar(4000) NOT NULL,
  `date` varchar(20) NOT NULL,
  `world` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

