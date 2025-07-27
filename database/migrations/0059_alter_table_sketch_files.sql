alter table sketch_files
modify file_id INT not null auto_increment;

insert into db_change
values (
  59,
  'Update file_id column to be an int rather than smallint.',
  '0059_alter_table_sketch_files.sql',
  now()
);
