-- Lookup rows the tests expect to exist (statuses, types, the mailing lists).
-- Load into the test database after pyangelo-schema.sql. Tables the tests
-- fill themselves (country, currency, tutorial levels...) are left empty
-- here; reference-data.sql adds those for a dev database.
-- Generated from the rows the numbered migrations insert.

SET foreign_key_checks=0;
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (1,'Undetermined','Undetermined');
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (2,'Permanent','General');
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (3,'Permanent','NoEmail');
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (4,'Permanent','Suppressed');
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (5,'Transient','General');
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (6,'Transient','MailboxFull');
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (7,'Transient','MessageTooLarge');
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (8,'Transient','ContentRejected');
INSERT INTO `bounce_type` (`bounce_type_id`, `bounce_type`, `bounce_sub_type`) VALUES (9,'Transient','AttachmentRejected');
INSERT INTO `campaign_status` (`campaign_status_id`, `status`) VALUES (1,'Draft');
INSERT INTO `campaign_status` (`campaign_status_id`, `status`) VALUES (2,'Sending');
INSERT INTO `campaign_status` (`campaign_status_id`, `status`) VALUES (3,'Sent');
INSERT INTO `caption_language` (`caption_language_id`, `language`, `srclang`) VALUES (1,'English','en');
INSERT INTO `caption_language` (`caption_language_id`, `language`, `srclang`) VALUES (2,'French','fr');
INSERT INTO `caption_language` (`caption_language_id`, `language`, `srclang`) VALUES (3,'German','de');
INSERT INTO `caption_language` (`caption_language_id`, `language`, `srclang`) VALUES (4,'Italian','it');
INSERT INTO `caption_language` (`caption_language_id`, `language`, `srclang`) VALUES (5,'Polish','pl');
INSERT INTO `caption_language` (`caption_language_id`, `language`, `srclang`) VALUES (6,'Russian','ru');
INSERT INTO `caption_language` (`caption_language_id`, `language`, `srclang`) VALUES (7,'Slovenian','sl');
INSERT INTO `caption_language` (`caption_language_id`, `language`, `srclang`) VALUES (8,'Spanish','es');
INSERT INTO `email_activity_type` (`activity_type_id`, `activity_type`) VALUES (1,'Sent');
INSERT INTO `email_activity_type` (`activity_type_id`, `activity_type`) VALUES (2,'Opened');
INSERT INTO `email_activity_type` (`activity_type_id`, `activity_type`) VALUES (3,'Bounced');
INSERT INTO `email_activity_type` (`activity_type_id`, `activity_type`) VALUES (4,'Marked as spam');
INSERT INTO `email_activity_type` (`activity_type_id`, `activity_type`) VALUES (5,'Clicked a link');
INSERT INTO `email_activity_type` (`activity_type_id`, `activity_type`) VALUES (6,'Unsubscribed');
INSERT INTO `email_status` (`email_status_id`, `email_status`) VALUES (1,'active');
INSERT INTO `email_status` (`email_status_id`, `email_status`) VALUES (2,'bounced');
INSERT INTO `email_status` (`email_status_id`, `email_status`) VALUES (3,'complained');
INSERT INTO `from_email` (`from_email_id`, `email`) VALUES (1,'Jeff Plumb <jeff@nocturnalrage.com>');
INSERT INTO `list` (`list_id`, `list_name`, `created_at`, `updated_at`) VALUES (1,'Free Newsletter','2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `list` (`list_id`, `list_name`, `created_at`, `updated_at`) VALUES (2,'Premium Newsletter','2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `mail_queue_status` (`mail_queue_status_id`, `status`) VALUES (1,'queued');
INSERT INTO `mail_queue_status` (`mail_queue_status_id`, `status`) VALUES (2,'sent');
INSERT INTO `mail_queue_status` (`mail_queue_status_id`, `status`) VALUES (3,'failed');
INSERT INTO `mastery_level` (`mastery_level_id`, `mastery_level_desc`, `points`, `created_at`, `updated_at`) VALUES (1,'Attempted',0,'2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `mastery_level` (`mastery_level_id`, `mastery_level_desc`, `points`, `created_at`, `updated_at`) VALUES (2,'Familiar',50,'2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `mastery_level` (`mastery_level_id`, `mastery_level_desc`, `points`, `created_at`, `updated_at`) VALUES (3,'Proficient',80,'2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `mastery_level` (`mastery_level_id`, `mastery_level_desc`, `points`, `created_at`, `updated_at`) VALUES (4,'Mastered',100,'2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `quiz_type` (`quiz_type_id`, `description`, `num_questions`, `created_at`, `updated_at`) VALUES (1,'Skill Quiz',7,'2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `quiz_type` (`quiz_type_id`, `description`, `num_questions`, `created_at`, `updated_at`) VALUES (2,'Tutorial Quiz',20,'2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `quiz_type` (`quiz_type_id`, `description`, `num_questions`, `created_at`, `updated_at`) VALUES (3,'Tutorial Category Quiz',30,'2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `quiz_type` (`quiz_type_id`, `description`, `num_questions`, `created_at`, `updated_at`) VALUES (4,'PyAngelo Mastery Quiz',50,'2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `segment` (`segment_id`, `segment_name`, `list_id`, `autoresponder_where_condition`) VALUES (1,'All Members',1,'1=1');
INSERT INTO `segment` (`segment_id`, `segment_name`, `list_id`, `autoresponder_where_condition`) VALUES (2,'Free Members',1,'1=1');
INSERT INTO `skill_question_type` (`skill_question_type_id`, `description`, `created_at`, `updated_at`) VALUES (1,'Multiple choice','2026-10-03 22:11:24','2026-10-03 22:11:24');
INSERT INTO `stripe_payment_type` (`payment_type_id`, `payment_type_name`) VALUES (1,'Payment');
INSERT INTO `stripe_payment_type` (`payment_type_id`, `payment_type_name`) VALUES (2,'Refund');
INSERT INTO `subscriber_status` (`subscriber_status_id`, `description`) VALUES (1,'Subscribed');
INSERT INTO `subscriber_status` (`subscriber_status_id`, `description`) VALUES (2,'Unsubscribed');
INSERT INTO `subscriber_status` (`subscriber_status_id`, `description`) VALUES (3,'Bounced');
INSERT INTO `subscriber_status` (`subscriber_status_id`, `description`) VALUES (4,'Marked as spam');
SET foreign_key_checks=1;
