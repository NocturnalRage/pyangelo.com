CREATE TABLE person (
  person_id int unsigned NOT NULL AUTO_INCREMENT,
  given_name varchar(100) NOT NULL,
  family_name varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  email_status_id smallint unsigned NOT NULL,
  bounce_count smallint unsigned NOT NULL,
  active boolean NOT NULL,
  country_code varchar(2) NOT NULL,
  detected_country_code varchar(2) NOT NULL,
  admin boolean DEFAULT 0 NOT NULL,
  last_login datetime DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (person_id),
  UNIQUE KEY email (email),
  FOREIGN KEY (email_status_id) REFERENCES email_status(email_status_id),
  FOREIGN KEY (country_code) REFERENCES country(country_code)
) COMMENT='People with a PyAngelo account.';

insert into db_change
values (
  4,
  'Create the peson table to hold all PyAngelo users.',
  '0004_create_person_table.sql',
  now()
);
