alter table sketch add column layout VARCHAR(4) not null default 'cols' after title;

update sketch set layout = 'cols' where layout = '';

insert into db_change
values (
  49,
  'Add a layout column to the sketch table.',
  '0049_add_layout_column_to_sketch_table.sql',
  now()
);
