CREATE TABLE password_reset_request (
  person_id int unsigned NOT NULL,
  token varchar(255) NOT NULL,
  processed boolean NOT NULL,
  created_at datetime NOT NULL,
  processed_at datetime NULL,
  PRIMARY KEY (person_id,token,created_at),
  KEY pass_reset_check (token,processed,created_at),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Password reset requests and a token to validate them.';

insert into db_change
values (
  7,
  'Create the password_reset_request table to validate password resets.',
  '0007_create_password_reset_request_table.sql',
  now()
);
