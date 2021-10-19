CREATE TABLE sketch_collection (
  collection_id int unsigned NOT NULL AUTO_INCREMENT,
  person_id int unsigned NOT NULL,
  collection_name varchar(100) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (`collection_id`),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
);

alter table sketch add column collection_id int unsigned NULL after person_id;
alter table sketch add foreign key (collection_id) references sketch_collection(collection_id);

insert into db_change
values (
  51,
  'Create table to store collection names.',
  '0051_create_sketch_collection_table.sql',
  now()
);
