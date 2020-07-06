CREATE TABLE lesson_completed (
  person_id int unsigned NOT NULL,
  lesson_id int unsigned NOT NULL,
  completed_at datetime NOT NULL,
  PRIMARY KEY (person_id, lesson_id),
  KEY lesson (lesson_id, person_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (lesson_id) REFERENCES lesson(lesson_id)
) COMMENT='Records the date a person completed a lesson.';

insert into db_change
values (
  13,
  'Create the lesson_completed table.',
  '0013_create_lesson_completed_table.sql',
  now()
);
