CREATE TABLE list (
  list_id int unsigned NOT NULL AUTO_INCREMENT,
  list_name varchar(50) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (list_id)
) COMMENT='Email lists that people can subscribe to.';

INSERT INTO list VALUES
(1,'Free Newsletter',now(),now());

CREATE TABLE subscriber_status (
  subscriber_status_id smallint unsigned NOT NULL,
  description varchar(50) NOT NULL,
  PRIMARY KEY (subscriber_status_id)
) COMMENT='Records the status of a subscriber';

INSERT INTO subscriber_status VALUES
(1,'Subscribed'),
(2,'Unsubscribed'),
(3,'Bounced'),
(4,'Marked as spam');

CREATE TABLE subscriber (
  list_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  subscriber_status_id smallint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  subscribed_at datetime NOT NULL,
  last_campaign_at datetime NOT NULL,
  last_autoresponder_at datetime NOT NULL,
  PRIMARY KEY (list_id, person_id),
  KEY subscriber_person (person_id),
  FOREIGN KEY (list_id) REFERENCES list(list_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (subscriber_status_id) REFERENCES subscriber_status(subscriber_status_id)
) COMMENT='Show which email lists a person has subscribed to.';

insert into db_change
values (
  9,
  'Create subscriber related tables for handling email newsletters.',
  '0009_create_subscriber_table.sql',
  now()
);
