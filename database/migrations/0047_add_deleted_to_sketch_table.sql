ALTER table sketch
ADD COLUMN deleted BOOLEAN NOT NULL DEFAULT 0 AFTER title;

ALTER table sketch
ADD COLUMN deleted_at DATETIME NULL AFTER deleted;

insert into db_change
values (
  47,
  'Add deleted column to sketch table.',
  '0047_add_deleted_to_sketch_table.sql',
  now()
);
