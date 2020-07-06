CREATE TABLE caption_language (
  caption_language_id int unsigned NOT NULL,
  language varchar(50) NOT NULL,
  srclang varchar(2) NOT NULL,
  PRIMARY KEY (caption_language_id),
  UNIQUE KEY (language),
  UNIQUE KEY (srclang)
) COMMENT='The languages the website can accept captions in.';

INSERT INTO caption_language values (1, 'English', 'en');
INSERT INTO caption_language values (2, 'French', 'fr');
INSERT INTO caption_language values (3, 'German', 'de');
INSERT INTO caption_language values (4, 'Italian', 'it');
INSERT INTO caption_language values (5, 'Polish', 'pl');
INSERT INTO caption_language values (6, 'Russian', 'ru');
INSERT INTO caption_language values (7, 'Slovenian', 'sl');
INSERT INTO caption_language values (8, 'Spanish', 'es');

CREATE TABLE lesson_caption(
  lesson_id int unsigned NOT NULL,
  caption_language_id int unsigned NOT NULL,
  caption_filename varchar(255) NOT NULL,
  PRIMARY KEY (lesson_id, caption_language_id),
  FOREIGN KEY (lesson_id) REFERENCES lesson(lesson_id),
  FOREIGN KEY (caption_language_id) REFERENCES caption_language(caption_language_id)
) COMMENT='Captions for each lesson.';

insert into db_change
values (
  20,
  'Create the table to hold captions for lessons.',
  '0020_create_lesson_caption_table.sql',
  now()
);
