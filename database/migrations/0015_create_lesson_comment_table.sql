CREATE TABLE lesson_comment (
  comment_id int unsigned NOT NULL AUTO_INCREMENT,
  lesson_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  lesson_comment text NOT NULL,
  published boolean NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (comment_id),
  KEY lesson_comment (lesson_id, created_at),
  KEY person_comment (person_id),
  FOREIGN KEY (lesson_id) REFERENCES lesson(lesson_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Stores comments for a lesson.';

insert into db_change
values (
  15,
  'Create the lesson_comment table.',
  '0015_create_lesson_comment_table.sql',
  now()
);
