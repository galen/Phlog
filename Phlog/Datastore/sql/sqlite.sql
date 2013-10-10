CREATE TABLE `comments` (
`id` integer not null,
`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`post_id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`text` text NOT NULL,
`email` text,
PRIMARY KEY (`id`)
);


CREATE TABLE `post_attributes` (
`post_id` int(11) NOT NULL,
`attribute` varchar(255) NOT NULL,
`value` text NOT NULL
);


CREATE TABLE `posts` (
`id` integer not null,
`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`title` varchar(255) NOT NULL,
`text` text NOT NULL,
`active` INTEGER,
PRIMARY KEY (`id`)
);
