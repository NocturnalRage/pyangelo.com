CREATE TABLE question_type (
  question_type_id tinyint unsigned NOT NULL,
  description varchar(30) DEFAULT NULL,
  category_slug varchar(30) NOT NULL,
  PRIMARY KEY (`question_type_id`)
);

INSERT INTO question_type values
(1, 'General', 'general'),
(2, 'PyAngelo', 'pyangelo'),
(3, 'Tutorials', 'tutorials'),
(4, 'Alogorithms', 'algorithms'),
(5, 'Loops', 'loops'),
(6, 'Conditionals', 'conditionals'),
(7, 'Variables', 'variables'),
(8, 'Functions', 'functions'),
(9, 'Website', 'website'),
(10, 'Discussion', 'discussion');

CREATE TABLE question (
  question_id int unsigned NOT NULL AUTO_INCREMENT,
  person_id int unsigned NOT NULL,
  question_title varchar(100) NOT NULL,
  question text NOT NULL,
  answer text,
  teacher_id int unsigned NOT NULL,
  created_at datetime NOT NULL,
  answered_at datetime NULL,
  updated_at datetime NOT NULL,
  published boolean NOT NULL,
  question_type_id tinyint unsigned NOT NULL,
  slug varchar(105) DEFAULT NULL,
  PRIMARY KEY (question_id),
  UNIQUE KEY slug (slug),
  KEY person (person_id),
  KEY updated_at (updated_at, published),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (teacher_id) REFERENCES person(person_id),
  FOREIGN KEY (question_type_id) REFERENCES question_type(question_type_id)
);

CREATE TABLE question_comment (
  comment_id int unsigned NOT NULL AUTO_INCREMENT,
  question_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  question_comment text NOT NULL,
  published boolean NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (comment_id),
  KEY question (question_id, created_at),
  KEY person (person_id),
  FOREIGN KEY (question_id) REFERENCES question(question_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
);

CREATE TABLE question_favourited (
  person_id int unsigned NOT NULL,
  question_id int unsigned NOT NULL,
  favourited_at datetime NOT NULL,
  PRIMARY KEY (person_id, question_id),
  KEY question_favourite (question_id, person_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (question_id) REFERENCES question(question_id)
) COMMENT='Records the date a person favourited a question.';

CREATE TABLE question_alert(
  question_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (question_id, person_id),
  FOREIGN KEY (question_id) REFERENCES question(question_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Alert the user if a comment is added to this question.';

insert into db_change
values (
  34,
  'Create the question tables for the ask the teacher section.',
  '0034_create_question_tables.sql',
  now()
);
