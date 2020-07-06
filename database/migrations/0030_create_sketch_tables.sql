CREATE TABLE sketch (
  sketch_id int unsigned NOT NULL AUTO_INCREMENT,
  person_id int unsigned NOT NULL,
  title varchar(100) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (sketch_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Holds all sketches.';

CREATE TABLE sketch_files (
  file_id smallint unsigned NOT NULL AUTO_INCREMENT,
  sketch_id int unsigned NOT NULL,
  filename varchar(255) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (file_id),
  UNIQUE KEY sketch_id_filename (sketch_id, filename),
  FOREIGN KEY (sketch_id) REFERENCES sketch(sketch_id)
) COMMENT='Holds details of all files for a sketch.';

insert into db_change
values (
  30,
  'Create sketch tables for tracking programs.',
  '0030-create-sketch-tables.sql',
  now()
);
