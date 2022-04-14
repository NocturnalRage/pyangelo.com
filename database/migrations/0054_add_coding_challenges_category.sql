INSERT INTO tutorial_category
VALUES
(3, 'Coding Challenges', 'coding-challenges', 3);

insert into db_change
values (
  54,
  'Add a coding challenges category to the tutorial_categories table.',
  '0054_add_coding_challenges_category.sql',
  now()
);
