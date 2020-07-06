CREATE TABLE blog_alert(
  blog_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (blog_id, person_id),
  FOREIGN KEY (blog_id) REFERENCES blog(blog_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Alert the user if a comment is added to this blog.';

CREATE TABLE lesson_alert(
  lesson_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (lesson_id, person_id),
  FOREIGN KEY (lesson_id) REFERENCES lesson(lesson_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Alert the user if a comment is added to this lesson.';

CREATE TABLE notification(
  notification_id int unsigned NOT NULL AUTO_INCREMENT,
  notification_type_id int unsigned NOT NULL,
  notification_type varchar(50) NOT NULL,
  person_id int unsigned NOT NULL,
  data text NOT NULL,
  has_been_read BOOLEAN NOT NULL DEFAULT FALSE,
  read_at datetime NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (notification_id),
  KEY person_read_at (person_id, has_been_read),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT="Stores notifications for a user";

insert into db_change
values (
  29,
  'Create alert tables for blogs and lessons.',
  '0029-create-alert-tables.sql',
  now()
);
