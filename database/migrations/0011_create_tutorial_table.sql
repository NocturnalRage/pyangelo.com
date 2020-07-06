CREATE TABLE tutorial_level (
  tutorial_level_id smallint unsigned NOT NULL AUTO_INCREMENT,
  description varchar(20) NOT NULL,
  PRIMARY KEY (tutorial_level_id)
) COMMENT='Beginner, intermediate, or advanced will be the levels.';

INSERT INTO tutorial_level
VALUES
(1, 'Beginner'),
(2, 'Intermediate'),
(3, 'Advanced');

CREATE TABLE tutorial_category (
  tutorial_category_id smallint unsigned NOT NULL AUTO_INCREMENT,
  category varchar(25) NOT NULL,
  category_slug varchar(25) NOT NULL,
  display_order smallint unsigned NOT NULL,
  PRIMARY KEY (tutorial_category_id),
  UNIQUE KEY (category_slug)
) COMMENT='A way to categorise tutorials into groups.';

INSERT INTO tutorial_category
VALUES
(1, 'Introduction to PyAngelo', 'introduction-to-pyangelo', 1);

CREATE TABLE tutorial (
  tutorial_id int unsigned NOT NULL AUTO_INCREMENT,
  title varchar(100) NOT NULL,
  description varchar(1000) NOT NULL,
  slug varchar(105) NOT NULL,
  thumbnail varchar(255) NOT NULL,
  pdf varchar(255) NULL,
  tutorial_category_id smallint unsigned NOT NULL,
  tutorial_level_id smallint unsigned NOT NULL,
  display_order smallint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (tutorial_id),
  UNIQUE KEY slug (slug),
  UNIQUE KEY title (title),
  FOREIGN KEY (tutorial_category_id) REFERENCES tutorial_category(tutorial_category_id),
  FOREIGN KEY (tutorial_level_id) REFERENCES tutorial_level(tutorial_level_id)
) COMMENT='Every tutorial will have a number of video lessons.';

insert into db_change
values (
  11,
  'Create the tutorial table.',
  '0011_create_tutorial_table.sql',
  now()
);
