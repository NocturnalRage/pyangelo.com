alter table lesson drop foreign key lesson_ibfk_2;
alter table sketch_files drop foreign key sketch_files_ibfk_1;
alter table tutorial drop foreign key tutorial_ibfk_3;

alter table lesson modify lesson_sketch_id VARCHAR(32) null;
alter table sketch_files modify sketch_id VARCHAR(32) not null;
alter table tutorial modify tutorial_sketch_id VARCHAR(32) null;

alter table sketch modify sketch_id VARCHAR(32) not null;
alter table lesson add foreign key (lesson_sketch_id) references sketch(sketch_id);
alter table sketch_files add foreign key (sketch_id) references sketch(sketch_id);
alter table tutorial add foreign key (tutorial_sketch_id) references sketch(sketch_id);

insert into db_change
values (
  53,
  'Add a slug column to the sketch table.',
  '0053_add_slug_column_to_sketch_table.sql',
  now()
);
