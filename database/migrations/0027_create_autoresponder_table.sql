CREATE TABLE autoresponder(
  autoresponder_id int unsigned NOT NULL AUTO_INCREMENT,
  segment_id tinyint unsigned NOT NULL,
  from_email_id smallint unsigned NOT NULL,
  subject varchar(200) NOT NULL,
  body_text text NOT NULL,
  body_html text NOT NULL,
  duration int unsigned NOT NULL,
  period varchar(50) NOT NULL,
  delay_in_minutes int unsigned NOT NULL,
  active boolean NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (autoresponder_id),
  FOREIGN KEY (segment_id) REFERENCES segment(segment_id),
  FOREIGN KEY (from_email_id) REFERENCES from_email(from_email_id)
) COMMENT='PyAngelo Autoreponder emails.';

CREATE TABLE autoresponder_activity(
  activity_id BIGINT unsigned NOT NULL AUTO_INCREMENT,
  autoresponder_id INT unsigned NOT NULL,
  person_id INT unsigned NOT NULL,
  activity_type_id SMALLINT unsigned NOT NULL,
  created_at DATETIME NOT NULL,
  aws_message_id varchar(100) NULL,
  link_id INT unsigned NULL,
  bounce_type_id SMALLINT unsigned NULL,
  PRIMARY KEY (activity_id),
  KEY person (person_id),
  KEY autoresponder_person (autoresponder_id, person_id, created_at),
  UNIQUE KEY aws_message (aws_message_id),
  FOREIGN KEY (autoresponder_id) REFERENCES autoresponder(autoresponder_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (activity_type_id) REFERENCES email_activity_type(activity_type_id),
  FOREIGN KEY (link_id) REFERENCES trackable_link(link_id),
  FOREIGN KEY (bounce_type_id) REFERENCES bounce_type(bounce_type_id)
) COMMENT='Holds all activities that people take on our autoresponder emails.';

insert into db_change
values (
  27,
  'Create the tables related to autoresponder emails.',
  '0027_create_autoresponder_table.sql',
  now()
);
