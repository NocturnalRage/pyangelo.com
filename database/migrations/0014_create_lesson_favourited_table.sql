CREATE TABLE lesson_favourited (
  person_id int unsigned NOT NULL,
  lesson_id int unsigned NOT NULL,
  favourited_at datetime NOT NULL,
  PRIMARY KEY (person_id, lesson_id),
  KEY lesson (lesson_id, person_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (lesson_id) REFERENCES lesson(lesson_id)
) COMMENT='Records the date a person favourited a lesson.';

insert into db_change
values (
  14,
  'Create the lesson_favourited table.',
  '0014_create_lesson_favourited_table.sql',
  now()
);
