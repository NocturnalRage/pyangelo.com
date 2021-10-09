RENAME TABLE tutorial_quiz TO quiz,
             tutorial_quiz_question TO quiz_question;

ALTER TABLE quiz RENAME COLUMN tutorial_quiz_id TO quiz_id;
ALTER TABLE quiz_question RENAME COLUMN tutorial_quiz_id TO quiz_id;

CREATE TABLE quiz_type (
  quiz_type_id tinyint unsigned NOT NULL AUTO_INCREMENT,
  description varchar(30) NOT NULL,
  num_questions tinyint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (quiz_type_id)
);

INSERT INTO quiz_type values(1, 'Skill Quiz', 7, now(), now());
INSERT INTO quiz_type values(2, 'Tutorial Quiz', 20, now(), now());
INSERT INTO quiz_type values(3, 'Tutorial Category Quiz', 30, now(), now());
INSERT INTO quiz_type values(4, 'PyAngelo Mastery Quiz', 50, now(), now());

ALTER table quiz
ADD COLUMN quiz_type_id tinyint unsigned NOT NULL
AFTER quiz_id;

ALTER TABLE skill
ADD COLUMN slug varchar(105) NOT NULL
AFTER skill_name;

CREATE UNIQUE INDEX skill_slug_u1 on skill(slug);

ALTER TABLE quiz
MODIFY COLUMN tutorial_id int unsigned NULL;

ALTER TABLE quiz
ADD COLUMN tutorial_category_id smallint unsigned NULL
AFTER tutorial_id;

ALTER TABLE quiz ADD CONSTRAINT quiz_ibfk_3 FOREIGN KEY (tutorial_category_id) REFERENCES tutorial_category(tutorial_category_id);

ALTER TABLE quiz
ADD COLUMN skill_id int unsigned NULL
AFTER quiz_type_id;

ALTER TABLE quiz ADD CONSTRAINT quiz_ibfk_4 FOREIGN KEY (skill_id) REFERENCES skill(skill_id);


UPDATE quiz set quiz_type_id = 2;

ALTER TABLE quiz ADD CONSTRAINT quiz_ibfk_5 FOREIGN KEY (quiz_type_id) REFERENCES quiz_type(quiz_type_id);

insert into db_change
values (
  50,
  'Rename the tutorial_quiz table.',
  '0050_rename_tutorial_quiz_table.sql',
  now()
);
