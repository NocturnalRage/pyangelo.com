CREATE TABLE membership_activate (
  person_id int unsigned NOT NULL,
  email varchar(100) NOT NULL,
  token varchar(255) NOT NULL,
  processed boolean NOT NULL,
  created_at datetime NOT NULL,
  processed_at datetime NULL,
  PRIMARY KEY (token),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Free memebership requests and a token to validate.';

insert into db_change
values (
  5,
  'Create the membership_activate table to authenticate new users and their email address.',
  '0005_create_membership_activate_table.sql',
  now()
);
