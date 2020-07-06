CREATE TABLE lesson_security_level (
  lesson_security_level_id smallint unsigned NOT NULL AUTO_INCREMENT,
  description varchar(20) NOT NULL,
  PRIMARY KEY (lesson_security_level_id)
) COMMENT='Anyone, Free members, or Premium members.';

INSERT INTO lesson_security_level
VALUES
(1, 'Anyone'),
(2, 'Free members'),
(3, 'Premium members');

CREATE TABLE lesson (
  lesson_id int unsigned NOT NULL AUTO_INCREMENT,
  tutorial_id int unsigned NOT NULL,
  lesson_title varchar(100) NOT NULL,
  lesson_description varchar(1000) NOT NULL,
  video_name varchar(100) NOT NULL,
  youtube_url varchar(255) NULL,
  seconds int(11) NOT NULL,
  comment_count int unsigned NOT NULL,
  lesson_slug varchar(105) NOT NULL,
  lesson_security_level_id smallint unsigned NOT NULL,
  display_order smallint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (lesson_id),
  UNIQUE KEY lesson_slug (lesson_slug),
  UNIQUE KEY lesson_title (lesson_title),
  KEY tutorial (tutorial_id),
  KEY previous_next (tutorial_id, display_order),
  FOREIGN KEY (lesson_security_level_id) REFERENCES lesson_security_level(lesson_security_level_id)
) COMMENT='Individual video lessons to be watched.';

insert into db_change
values (
  12,
  'Create the lesson table.',
  '0012_create_lesson_table.sql',
  now()
);
