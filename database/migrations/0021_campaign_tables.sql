CREATE TABLE from_email (
  from_email_id smallint unsigned NOT NULL,
  email varchar(100) NOT NULL,
  PRIMARY KEY (from_email_id)
) COMMENT='Authorised email addresses for sending campaigns and autoresponders.';
INSERT INTO from_email values (1, 'Jeff Plumb <jeff@nocturnalrage.com>');

CREATE TABLE campaign_status (
  campaign_status_id tinyint unsigned NOT NULL,
  status varchar(20) NOT NULL,
  PRIMARY KEY (campaign_status_id)
) COMMENT='Indicates if a campaign is a draft, in sending mode, or sent.';
INSERT INTO campaign_status values (1, 'Draft');
INSERT INTO campaign_status values (2, 'Sending');
INSERT INTO campaign_status values (3, 'Sent');


CREATE TABLE campaign(
  campaign_id int unsigned NOT NULL AUTO_INCREMENT,
  campaign_status_id tinyint unsigned NOT NULL,
  list_id int unsigned NOT NULL,
  from_email_id smallint unsigned NOT NULL,
  subject varchar(200) NOT NULL,
  body_text text NOT NULL,
  body_html text NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (campaign_id),
  FOREIGN KEY (campaign_status_id) REFERENCES campaign_status(campaign_status_id),
  FOREIGN KEY (list_id) REFERENCES list(list_id),
  FOREIGN KEY (from_email_id) REFERENCES from_email(from_email_id)
) COMMENT='Captions for each lesson.';

CREATE TABLE trackable_link (
  link_id INT unsigned NOT NULL AUTO_INCREMENT,
  href varchar(255) NOT NULL,
  PRIMARY KEY (link_id),
  UNIQUE KEY href (href)
) COMMENT='Trackable links that allow us to count how many times a link is clicked and redirect to the correct page.';

CREATE TABLE email_activity_type (
  activity_type_id SMALLINT unsigned NOT NULL,
  activity_type varchar(20) NOT NULL,
  PRIMARY KEY (activity_type_id)
) COMMENT='These are the user actions we track for a campaign.';

INSERT INTO email_activity_type (activity_type_id, activity_type)
VALUES
(1, 'Sent'),
(2, 'Opened'),
(3, 'Bounced'),
(4, 'Marked as spam'),
(5, 'Clicked a link'),
(6, 'Unsubscribed');

CREATE TABLE bounce_type (
  bounce_type_id SMALLINT unsigned NOT NULL,
  bounce_type varchar(20) NOT NULL,
  bounce_sub_type varchar(20) NOT NULL,
  PRIMARY KEY (bounce_type_id)
) COMMENT='These are the different bounce types from Amazon SES.';

INSERT INTO bounce_type (bounce_type_id, bounce_type, bounce_sub_type)
VALUES
(1, 'Undetermined', 'Undetermined'),
(2, 'Permanent', 'General'),
(3, 'Permanent', 'NoEmail'),
(4, 'Permanent', 'Suppressed'),
(5, 'Transient', 'General'),
(6, 'Transient', 'MailboxFull'),
(7, 'Transient', 'MessageTooLarge'),
(8, 'Transient', 'ContentRejected'),
(9, 'Transient', 'AttachmentRejected');

CREATE TABLE campaign_activity(
  activity_id BIGINT unsigned NOT NULL AUTO_INCREMENT,
  campaign_id INT unsigned NOT NULL,
  person_id INT unsigned NOT NULL,
  activity_type_id SMALLINT unsigned NOT NULL,
  created_at DATETIME NOT NULL,
  aws_message_id varchar(100) NULL,
  link_id INT unsigned NULL,
  bounce_type_id SMALLINT unsigned NULL,
  PRIMARY KEY (activity_id),
  KEY person (person_id),
  KEY campaign_person (campaign_id, person_id, created_at),
  UNIQUE KEY aws_message (aws_message_id),
  FOREIGN KEY (campaign_id) REFERENCES campaign(campaign_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (activity_type_id) REFERENCES email_activity_type(activity_type_id),
  FOREIGN KEY (link_id) REFERENCES trackable_link(link_id),
  FOREIGN KEY (bounce_type_id) REFERENCES bounce_type(bounce_type_id)
) COMMENT='Holds all activities that people take on our campaign emails.';

insert into db_change
values (
  21,
  'Create the tables related to campaign emails.',
  '0021_campaign_tables.sql',
  now()
);
