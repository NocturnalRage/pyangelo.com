ALTER table lesson
ADD COLUMN poster varchar(255) AFTER display_order;

insert into db_change
values (
  19,
  'Add poster column to the lesson table.',
  '0019_add_poster_to_lesson_table.sql',
  now()
);
