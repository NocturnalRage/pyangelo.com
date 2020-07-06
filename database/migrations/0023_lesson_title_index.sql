DROP INDEX lesson_title ON lesson;

CREATE UNIQUE INDEX lesson_title_tutorial_id ON lesson(tutorial_id, lesson_title);

insert into db_change
values (
  23,
  'Change lesson_title index to be unique for a tutorial',
  '0023_lesson_title_index.sql',
  now()
);
