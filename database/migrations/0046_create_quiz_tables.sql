CREATE TABLE skill (
  skill_id int unsigned NOT NULL AUTO_INCREMENT,
  tutorial_id int unsigned NOT NULL,
  skill_name varchar(100) DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (`skill_id`),
  KEY tutorial (tutorial_id),
  FOREIGN KEY (tutorial_id) REFERENCES tutorial(tutorial_id)
);

CREATE TABLE skill_question_type (
  skill_question_type_id tinyint unsigned NOT NULL AUTO_INCREMENT,
  description varchar(100) DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (`skill_question_type_id`)
);

INSERT INTO  skill_question_type VALUES (1, 'Multiple choice', now(), now());

CREATE TABLE skill_question (
  skill_question_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_id int unsigned NOT NULL,
  skill_question_type_id tinyint unsigned NOT NULL,
  question text NOT NULL,
  question_image varchar(255) NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_question_id),
  FOREIGN KEY (skill_id) REFERENCES skill(skill_id),
  FOREIGN KEY (skill_question_type_id) REFERENCES skill_question_type(skill_question_type_id)
);

CREATE TABLE skill_question_option (
  skill_question_option_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_question_id int unsigned NOT NULL,
  option_text varchar(1000) NOT NULL,
  option_image varchar(255) NULL,
  option_order tinyint NOT NULL,
  correct bool NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_question_option_id),
  FOREIGN KEY (skill_question_id) REFERENCES skill_question(skill_question_id)
);

CREATE TABLE skill_question_hint (
  hint_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_question_id int unsigned NOT NULL,
  hint varchar(1000) NOT NULL,
  hint_image varchar(255) NULL,
  hint_order tinyint NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (hint_id),
  KEY skill_question (skill_question_id),
  FOREIGN KEY (skill_question_id) REFERENCES skill_question(skill_question_id)
);

CREATE TABLE mastery_level (
  mastery_level_id tinyint unsigned NOT NULL,
  mastery_level_desc varchar(20) NOT NULL,
  points tinyint NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (mastery_level_id)
);

INSERT INTO mastery_level VALUES (1, 'Attempted', 0, now(), now());
INSERT INTO mastery_level VALUES (2, 'Familiar', 50, now(), now());
INSERT INTO mastery_level VALUES (3, 'Proficient', 80, now(), now());
INSERT INTO mastery_level VALUES (4, 'Mastered', 100, now(), now());

CREATE TABLE skill_mastery (
  skill_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  mastery_level_id tinyint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_id, person_id),
  FOREIGN KEY (skill_id) REFERENCES skill(skill_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (mastery_level_id) REFERENCES mastery_level(mastery_level_id)
);

CREATE TABLE tutorial_quiz (
  tutorial_quiz_id int unsigned NOT NULL AUTO_INCREMENT,
  tutorial_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  created_at datetime NOT NULL,
  started_at datetime NULL,
  completed_at datetime NULL,
  PRIMARY KEY (tutorial_quiz_id),
  FOREIGN KEY (tutorial_id) REFERENCES tutorial(tutorial_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
);

CREATE TABLE tutorial_quiz_question (
  tutorial_quiz_id int unsigned NOT NULL,
  skill_question_id int unsigned NOT NULL,
  skill_question_option_id int unsigned NULL,
  correct_unaided boolean NULL,
  created_at datetime NOT NULL,
  started_at datetime NULL,
  answered_at datetime NULL,
  PRIMARY KEY (tutorial_quiz_id, skill_question_id),
  FOREIGN KEY (tutorial_quiz_id) REFERENCES tutorial_quiz(tutorial_quiz_id),
  FOREIGN KEY (skill_question_id) REFERENCES skill_question(skill_question_id),
  FOREIGN KEY (skill_question_option_id) REFERENCES skill_question_option(skill_question_option_id)
);

insert into db_change
values (
  46,
  'Create tables for quizzes.',
  '0046_create_quiz_tables.sql',
  now()
);
