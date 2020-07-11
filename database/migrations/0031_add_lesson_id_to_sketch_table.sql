alter table sketch add column lesson_id int unsigned NULL after person_id;
alter table sketch add foreign key (lesson_id) references lesson(lesson_id);

alter table sketch add column tutorial_id int unsigned NULL after lesson_id;
alter table sketch add foreign key (tutorial_id) references tutorial(tutorial_id);

insert into db_change
values (
  31,
  'Add a nullable lesson_id and tutorial_id column to the sketch table.',
  '0031_add_lesson_id_to_sketch_table.sql',
  now()
);
