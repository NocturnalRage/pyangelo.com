CREATE TABLE remember_me(
  person_id int unsigned NOT NULL,
  session_id varchar(255) NOT NULL,
  token varchar(255) NOT NULL,
  created_at datetime NOT NULL,
  expires_at datetime NOT NULL,
  PRIMARY KEY (person_id,session_id,token),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Remember me cookie information.';

insert into db_change
values (
  6,
  'Create the remember_me table to validate remember me cookies.',
  '0006_create_remember_me_table.sql',
  now()
);
