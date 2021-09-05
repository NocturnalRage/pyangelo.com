CREATE TABLE class (
  class_id int unsigned NOT NULL AUTO_INCREMENT,
  person_id int unsigned NOT NULL,
  class_name varchar(100) DEFAULT NULL,
  class_code varchar(40) DEFAULT NULL,
  archived BOOLEAN NOT NULL DEFAULT 0,
  archived_at DATETIME NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (`class_id`),
  UNIQUE KEY class_code (class_code),
  KEY person (person_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
);

CREATE TABLE class_student (
  class_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  joined_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (class_id, person_id),
  FOREIGN KEY (class_id) REFERENCES class(class_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
);

insert into db_change
values (
  48,
  'Create tables for classes.',
  '0048_create_class_tables.sql',
  now()
);
