alter table lesson drop column comment_count;

insert into db_change
values (
  16,
  'Drop the comment_count column from the lesson table.',
  '0016_drop_comment_count_from_lesson_table.sql',
  now()
);
