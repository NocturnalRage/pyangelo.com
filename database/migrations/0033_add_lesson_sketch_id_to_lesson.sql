alter table lesson add column lesson_sketch_id INT UNSIGNED NULL after lesson_security_level_id;

alter table lesson add foreign key (lesson_sketch_id) references sketch(sketch_id);

insert into db_change
values (
  33,
  'Add a the lesson_sketch_id to the lesson table for creating a starter sketch.',
  '0033_add_lesson_sketch_id_to_lesson.sql',
  now()
);
