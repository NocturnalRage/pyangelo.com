UPDATE question_type
SET    description = 'Algorithms'
WHERE  question_type_id = 4;

insert into db_change
values (
  36,
  'Fix the Algorithms question type (was Alogorithms).',
  '0036_fix_algorithms_question_type.sql',
  now()
);
