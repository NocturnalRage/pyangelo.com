CREATE TABLE email_status (
  email_status_id smallint unsigned NOT NULL AUTO_INCREMENT,
  email_status varchar(10) NOT NULL,
  PRIMARY KEY (email_status_id)
) COMMENT='You should check the email status before sending emails to a person.';

insert into email_status 
values
(1, 'active'),
(2, 'bounced'),
(3, 'complained');

insert into db_change
values (
  3,
  'Create the email_status table.',
  '0003_create_email_status_table.sql',
  now()
);
