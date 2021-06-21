SET foreign_key_checks = 0;

TRUNCATE table tutorial_category;

INSERT INTO tutorial_category
VALUES
(1, 'Getting Started', 'getting-started', 1);

INSERT INTO tutorial_category
VALUES
(2, 'Arcade Games', 'arcade-games', 2);

SET foreign_key_checks = 1;

insert into db_change
values (
  35,
  'Reset the values stored in the tutorial_categories table.',
  '0035_reset_tutorial_categories.sql',
  now()
);
