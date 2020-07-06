DROP INDEX lesson_slug ON lesson;

CREATE UNIQUE INDEX lesson_slug_tutorial_id ON lesson(tutorial_id, lesson_slug);

insert into db_change
values (
  24,
  'Change lesson_slug index to be unique for a tutorial',
  '0024_lesson_slug_index.sql',
  now()
);
