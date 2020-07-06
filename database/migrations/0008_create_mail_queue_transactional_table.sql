CREATE TABLE mail_queue_status (
  mail_queue_status_id smallint unsigned NOT NULL AUTO_INCREMENT,
  status varchar(10) NOT NULL,
  PRIMARY KEY (mail_queue_status_id)
) COMMENT='The status of messages in our mail queue tables.';

INSERT INTO mail_queue_status VALUES (1, 'queued'), (2, 'sent'), (3, 'failed');

CREATE TABLE mail_queue_transactional (
  mail_queue_transactional_id int unsigned NOT NULL AUTO_INCREMENT,
  from_email varchar(200) NOT NULL,
  reply_email varchar(200) DEFAULT NULL,
  to_email varchar(200) NOT NULL,
  subject varchar(200) NOT NULL,
  body_text text NOT NULL,
  body_html text NOT NULL,
  created_at datetime NOT NULL,
  sent_at datetime DEFAULT NULL,
  mail_queue_status_id smallint unsigned DEFAULT NULL,
  PRIMARY KEY (mail_queue_transactional_id),
  KEY mail_queue_status (mail_queue_status_id),
  FOREIGN KEY (mail_queue_status_id) REFERENCES mail_queue_status(mail_queue_status_id)
) COMMENT='Mail queue to hold transactional email messages.';

insert into db_change
values (
  8,
  'Create the mail queue transactional table to queue our transactional emails.',
  '0008_create_mail_queue_transactional_table.sql',
  now()
);
